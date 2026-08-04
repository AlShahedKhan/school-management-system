<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Exports\PaymentSlipExport;
use App\Models\AdmissionStudent;
use App\Models\School;
use App\Models\SchoolFeeTemplate;
use App\Models\SchoolPayment;
use App\Models\SchoolSession;
use App\Models\SchoolStudentFee;
use App\Models\SchoolFeeDiscount;
use App\Services\ExamDiscountApplicationService;
use App\Services\FeeStatusSyncService;
use App\Services\SchoolFeeDiscountService;
use App\Services\AccountService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class SchoolPaymentController extends Controller
{
    /**
     * Get the school associated with the authenticated user.
     */
    private function getSchool()
    {
        $school = School::where('user_id', Auth::id())->first();
        if (!$school) {
            abort(403, 'Unauthorized: No school associated with this account.');
        }
        return $school;
    }

    /**
     * Display a listing of payments scoped to the user's school.
     */
    public function index(Request $request)
    {
        $school = $this->getSchool();

        // Use camelCase to match your Model functions
        $query = SchoolPayment::with([
            'student.schoolClass',
            'student.schoolGroup',
            'student.schoolSection',
            'student.schoolSession'
        ])->where('school_id', $school->id);

        if ($request->search) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->whereHas('student', function ($sub) use ($s) {
                    $sub->where('student_name', 'like', "%$s%")
                        ->orWhere('student_id_number', 'like', "%$s%");
                })
                    ->orWhere('fees_type', 'like', "%$s%")
                    ->orWhere('pay_method', 'like', "%$s%");
            });
        }

        return response()->json($query->latest()->paginate(15));
    }

    /**
     * Recalculate and sync school_student_fees.status based on payment sum + date.
     */
    private function syncFeeStatus(SchoolStudentFee $feeRecord): void
    {
        app(FeeStatusSyncService::class)->syncSingle($feeRecord);
    }

    /**
     * Apply an exam-based discount on top of the base amount when the student's
     * achieved grade is equal to or higher than the configured qualifying grade
     * and the payment matches the discounted fee type.
     */
    private function applyExamDiscount(int $schoolId, int $studentId, float $amount, ?string $feesType, ?string $feeName): float
    {
        $examDiscount = SchoolFeeDiscount::where('school_id', $schoolId)
            ->where('student_id', $studentId)
            ->where('discount_scope', 'exam')
            ->orderByDesc('id')
            ->first();

        if (!$examDiscount) {
            return $amount;
        }

        // Match the discount's fee type against the payment being processed.
        // Legacy exam discounts without a fee type apply to all fees.
        if ($examDiscount->fee_type_id) {
            $discountFeeType = SchoolFeeTemplate::where('id', $examDiscount->fee_type_id)->first();

            if ($discountFeeType) {
                $typeMatches = $discountFeeType->fee_type_name === $feesType;
                $nameMatches = empty($discountFeeType->fee_name)
                    || empty($feeName)
                    || $discountFeeType->fee_name === $feeName;

                if (!$typeMatches || !$nameMatches) {
                    return $amount;
                }
            }
        }

        $qualifies = app(ExamDiscountApplicationService::class)
            ->studentQualifies($schoolId, $studentId, $examDiscount->minimum_grade);

        if (!$qualifies) {
            return $amount;
        }

        $value = (float) $examDiscount->discount_value;
        $discountAmount = $examDiscount->discount_type === 'Percentage'
            ? $amount * $value / 100
            : $value;

        return max($amount - $discountAmount, 0);
    }

    /**
     * Store a newly created payment.
     */
    public function store(Request $request)
    {
        $school = $this->getSchool();

        $validated = $request->validate([
            'admission_student_id' => 'required|exists:admission_students,id',
            'fees_type'            => 'required|string',
            'fee_name'             => 'required|string',
            'total_payable'        => 'required|numeric|min:0',
            'type_amount'          => 'required|numeric|min:0',
            'pay_date'             => 'required|date',
            'pay_method'           => 'required|string',
            'for_month'            => 'nullable|regex:/^\d{4}-\d{2}$/',
        ]);

        // Modified on 2026-07-09: Prevent collecting payments for inactive students
        $student = AdmissionStudent::findOrFail($validated['admission_student_id']);
        if ($student->status === 'Inactive') {
            return response()->json(['message' => 'Cannot collect payment for inactive students.'], 403);
        }

        // Apply session-scope then exam-scope discounts to get the real total payable
        $effectiveTotal = app(SchoolFeeDiscountService::class)->effectiveTotal(
            $school->id,
            (int) $validated['admission_student_id'],
            (float) $validated['total_payable'],
            $validated['fees_type'],
            $validated['fee_name'] ?? null
        );

        // Sum all previous payments for this student + fee combination
        $alreadyPaid = SchoolPayment::where('school_id', $school->id)
            ->where('admission_student_id', $validated['admission_student_id'])
            ->where('fees_type', $validated['fees_type'])
            ->where('fee_name', $validated['fee_name'])
            ->sum('type_amount');

        $paidAmount = (float) $validated['type_amount'];
        $totalPaid  = (float) $alreadyPaid + $paidAmount;
        $dueAmount  = max($effectiveTotal - $totalPaid, 0);

        // Link to the specific school_student_fee record if for_month is provided
        $schoolStudentFeeId = null;
        $feeRecord = null;
        if (!empty($validated['for_month'])) {
            $forDate = Carbon::parse($validated['for_month'] . '-01');
            $feeRecord = SchoolStudentFee::where('school_id', $school->id)
                ->where('student_id', $validated['admission_student_id'])
                ->where('fee_type_name', $validated['fees_type'])
                ->where('fee_name', $validated['fee_name'])
                ->whereYear('pay_date', $forDate->year)
                ->whereMonth('pay_date', $forDate->month)
                ->first();
            if ($feeRecord) {
                $schoolStudentFeeId = $feeRecord->id;
            }
        }

        $paymentStatus = 'unpaid';
        if ($totalPaid >= $effectiveTotal && $effectiveTotal > 0) {
            $paymentStatus = 'paid';
        } elseif ($paidAmount > 0 && $dueAmount > 0) {
            $paymentStatus = 'partial';
        }

        $payment = DB::transaction(function () use ($school, $validated, $schoolStudentFeeId, $effectiveTotal, $dueAmount, $paymentStatus, $paidAmount, $feeRecord) {
            $payment = SchoolPayment::create(array_merge($validated, [
                'school_id'             => $school->id,
                'school_student_fee_id' => $schoolStudentFeeId,
                'total_payable'         => $effectiveTotal,
                'payable_due'           => $dueAmount,
                'status'                => $paymentStatus,
                'total_amount'          => $paidAmount,
                'total_due'             => $dueAmount,
            ]));

            // Sync the linked fee record's status using the unified helper
            if ($feeRecord) {
                $this->syncFeeStatus($feeRecord);
            }

            // Cash In to the internal System Cash Balance (immutable ledger)
            app(AccountService::class)->cashIn(
                $school->id,
                $paidAmount,
                AccountService::moduleForFeeType($validated['fees_type'] ?? null),
                $payment->id,
                [
                    'before_discount' => $effectiveTotal,
                    'after_discount'  => $effectiveTotal,
                    'remarks'         => ($validated['fees_type'] ?? '') . ' - ' . ($validated['fee_name'] ?? '') . ' | Student #' . $validated['admission_student_id'],
                ]
            );

            return $payment;
        });

        return response()->json(['status' => 'success', 'data' => $payment->load('student')], 201);
    }

    public function show($id)
    {
        $school = $this->getSchool();

        // Use camelCase to match your Student model methods
        $payment = SchoolPayment::with([
            'student.schoolClass',
            'student.schoolGroup',
            'student.schoolSection',
            'student.schoolSession'
        ])
            ->where('school_id', $school->id)
            ->findOrFail($id);

        return response()->json($payment);
    }

    /**
     * Update the specified payment.
     */
    public function update(Request $request, $id)
    {
        $school = $this->getSchool();
        $payment = SchoolPayment::where('school_id', $school->id)->findOrFail($id);

        $validated = $request->validate([
            'fees_type'     => 'sometimes|required|string',
            'fee_name'      => 'sometimes|nullable|string',
            'total_payable' => 'sometimes|required|numeric|min:0',
            'type_amount'   => 'sometimes|required|numeric|min:0',
            'pay_date'      => 'sometimes|required|date',
            'pay_method'    => 'sometimes|required|string',
        ]);

        // Re-calculate dues if amounts are updated
        if (isset($validated['total_payable']) || isset($validated['type_amount'])) {
            $feesType  = $validated['fees_type']  ?? $payment->fees_type;
            $feeName   = $validated['fee_name']   ?? $payment->fee_name;
            $studentId = $payment->admission_student_id;

            // Apply session-scope then exam-scope discounts to get the real total payable
            $effectiveTotal = app(SchoolFeeDiscountService::class)->effectiveTotal(
                $school->id,
                (int) $studentId,
                (float) ($validated['total_payable'] ?? $payment->total_payable),
                $feesType,
                $feeName
            );

            // Sum all OTHER payments for this student + fee (excluding current record)
            $alreadyPaid = SchoolPayment::where('school_id', $school->id)
                ->where('admission_student_id', $studentId)
                ->where('fees_type', $feesType)
                ->where('fee_name', $feeName)
                ->where('id', '!=', $id)
                ->sum('type_amount');

            $paidAmount = (float) ($validated['type_amount'] ?? $payment->type_amount);
            $totalPaid  = (float) $alreadyPaid + $paidAmount;
            $due        = max($effectiveTotal - $totalPaid, 0);

            $validated['total_payable'] = $effectiveTotal;
            $validated['payable_due']   = $due;
            $validated['total_due']     = $due;
            $validated['total_amount']  = $paidAmount;

            // Recalculate status
            if ($totalPaid >= $effectiveTotal && $effectiveTotal > 0) {
                $validated['status'] = 'paid';
            } elseif ($paidAmount > 0 && $due > 0) {
                $validated['status'] = 'partial';
            } else {
                $validated['status'] = 'unpaid';
            }
        }

        $payment = DB::transaction(function () use ($school, $payment, $validated) {
            $oldAmount = (float) $payment->type_amount;

            $payment->update($validated);

            // Sync the linked fee record's status
            if ($payment->school_student_fee_id) {
                $feeRecord = SchoolStudentFee::find($payment->school_student_fee_id);
                if ($feeRecord) {
                    $this->syncFeeStatus($feeRecord);
                }
            }

            // Keep the internal ledger consistent with a Reverse + re-book.
            $newAmount = (float) $payment->type_amount;
            if (abs($newAmount - $oldAmount) > 0.0001) {
                $service = app(AccountService::class);
                $service->cashOut($school->id, $oldAmount, 'Payment Adjustment', $payment->id, [
                    'remarks' => 'Reversal of ' . ($payment->fees_type ?? 'fee') . ' payment #' . $payment->id
                        . ' (amount adjusted ' . number_format($oldAmount, 2) . ' -> ' . number_format($newAmount, 2) . ')',
                ]);
                $service->cashIn(
                    $school->id,
                    $newAmount,
                    AccountService::moduleForFeeType($payment->fees_type),
                    $payment->id,
                    [
                        'before_discount' => (float) $payment->total_payable,
                        'after_discount'  => (float) $payment->total_payable,
                        'remarks'         => ($payment->fees_type ?? '') . ' - ' . ($payment->fee_name ?? '') . ' (adjusted) | Student #' . $payment->admission_student_id,
                    ]
                );
            }

            return $payment;
        });

        return response()->json([
            'status'  => 'success',
            'message' => 'Payment updated successfully',
            'data'    => $payment->load('student')
        ]);
    }

    /**
     * Remove the specified payment.
     */
    public function destroy($id)
    {
        $school = $this->getSchool();
        $payment = SchoolPayment::where('school_id', $school->id)->findOrFail($id);

        DB::transaction(function () use ($school, $payment) {
            $amount = (float) $payment->type_amount;
            $paymentId = $payment->id;
            $feesType = $payment->fees_type;
            $studentId = $payment->admission_student_id;

            // Sync the fee record status before deleting
            if ($payment->school_student_fee_id) {
                $feeRecord = SchoolStudentFee::find($payment->school_student_fee_id);
            }

            $payment->delete();

            // Recalculate the fee record status after payment removal
            if (isset($feeRecord) && $feeRecord) {
                $this->syncFeeStatus($feeRecord);
            }

            // Reverse the ledger entry — ledger rows are never deleted.
            if ($amount > 0) {
                app(AccountService::class)->cashOut($school->id, $amount, 'Payment Adjustment', $paymentId, [
                    'remarks' => 'Reversal of deleted ' . ($feesType ?? 'fee') . ' payment #' . $paymentId . ' | Student #' . $studentId,
                ]);
            }
        });

        return response()->json([
            'status'  => 'success',
            'message' => 'Record deleted successfully'
        ]);
    }

    /**
     * Calculate the total payable amount for a student, 
     * applying discounts if they exist.
     */
    public function getTotalFee(Request $request)
    {
        $request->validate([
            'fees_type'    => 'required|string',
            'fee_name'     => 'required|string',
            'admission_id' => 'required|integer',
        ]);

        $school = $this->getSchool();

        $fee = null;
        $hasDiscount = false;
        $effectiveAmount = 0;

        // Load the student fee record to get the original amount
        $studentFee = SchoolStudentFee::where('school_id', $school->id)
            ->where('student_id', $request->admission_id)
            ->where('fee_type_name', $request->fees_type)
            ->where('fee_name', $request->fee_name)
            ->first();

        // 1. Check for active new-system discounts (discount_students) — pick best
        $newDiscount = null;
        if ($studentFee) {
            $allDiscounts = DB::table('discount_students')
                ->join('school_discounts', 'discount_students.discount_id', '=', 'school_discounts.id')
                ->where('discount_students.student_id', $request->admission_id)
                ->where('discount_students.status', 'active')
                ->where('school_discounts.is_active', true)
                ->where('school_discounts.school_id', $school->id)
                ->where('school_discounts.fee_template_id', $studentFee->fee_template_id)
                ->select('discount_students.*', 'school_discounts.discount_type', 'school_discounts.discount_value', 'school_discounts.months')
                ->get();

            $baseAmount = (float) $studentFee->base_amount;
            $feeMonth = $studentFee->pay_date ? Carbon::parse($studentFee->pay_date)->format('Y-m') : null;
            $bestAmount = $baseAmount;

            foreach ($allDiscounts as $d) {
                $discountMonths = $d->months ? json_decode($d->months, true) : null;
                if ($discountMonths && $feeMonth && !in_array($feeMonth, $discountMonths)) {
                    continue;
                }

                $candidate = $d->discount_type === 'Percentage'
                    ? $baseAmount - ($baseAmount * (float) $d->discount_value / 100)
                    : max($baseAmount - (float) $d->discount_value, 0);

                if ($candidate < $bestAmount) {
                    $bestAmount = $candidate;
                    $newDiscount = $d;
                }
            }

            if ($newDiscount) {
                $effectiveAmount = $bestAmount;
                $hasDiscount = true;
            }
        }

        // 2. Check for legacy session-scope discount (school_fee_discounts) if no new discount found.
        //    Exam-scope discounts are NOT applied here unconditionally — they only take effect
        //    through applyExamDiscount() once the exam result is published and the student's
        //    grade meets the minimum qualifying grade.
        if (!$hasDiscount) {
            $legacyDiscount = SchoolFeeDiscount::where('school_id', $school->id)
                ->where('student_id', $request->admission_id)
                ->where('fee_name', $request->fee_name)
                ->where('discount_scope', 'session')
                ->first();

            if ($legacyDiscount) {
                $effectiveAmount = (float) $legacyDiscount->after_discount;
                $hasDiscount = true;
            }
        }

        // Exam-based discount applied on top of the effective total
        if ($hasDiscount) {
            $effectiveAmount = $this->applyExamDiscount(
                $school->id,
                (int) $request->admission_id,
                $effectiveAmount,
                $request->fees_type,
                $request->fee_name ?? null
            );
        }

        if ($hasDiscount) {
            $alreadyPaid = SchoolPayment::where('school_id', $school->id)
                ->where('admission_student_id', $request->admission_id)
                ->where('fees_type', $request->fees_type)
                ->where('fee_name', $request->fee_name)
                ->sum('type_amount');

            $remainingDue = max($effectiveAmount - (float) $alreadyPaid, 0);

            return response()->json([
                'total_payable' => $effectiveAmount,
                'remaining_due' => $remainingDue,
                'has_discount'  => true,
            ]);
        }

        // 3. Fallback to standard fee amount — check student fees first, then templates
        if ($studentFee) {
            $fee = $studentFee;
        } else {
            $fee = SchoolFeeTemplate::where('school_id', $school->id)
                ->where('fee_type_name', $request->fees_type)
                ->where('fee_name', $request->fee_name)
                ->first();
        }

        if (!$fee) {
            return response()->json(['message' => 'Fee configuration not found.'], 404);
        }

        $paymentRecord = SchoolPayment::where('school_id', $school->id)
            ->where('school_id', $school->id)
            ->where('admission_student_id', $request->admission_id)
            ->where('fees_type', $request->fees_type)
            ->where('fee_name', $request->fee_name)
            ->sum('type_amount');

        $baseAmount = (float) $fee->base_amount;

        // Exam-based discount applied to the standard fee amount
        $discountedTotal = $this->applyExamDiscount(
            $school->id,
            (int) $request->admission_id,
            $baseAmount,
            $request->fees_type,
            $request->fee_name ?? null
        );
        $amountToBePaid = max($discountedTotal - (float) $paymentRecord, 0);

        return response()->json([
            'total_payable' => $discountedTotal,
            'remaining_due' => $amountToBePaid,
            'has_discount'  => $discountedTotal < $baseAmount,
        ]);
    }

    /**
     * Download a PDF payment report for a specific student.
     */
    public function downloadPdf(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:admission_students,id',
        ]);

        $school = $this->getSchool();

        $student = AdmissionStudent::with([
            'schoolClass',
            'schoolGroup',
            'schoolSection',
            'schoolSession',
        ])->findOrFail($request->student_id);

        $payments = SchoolPayment::where('school_id', $school->id)
            ->where('admission_student_id', $request->student_id)
            ->orderBy('pay_date', 'asc')
            ->get();

        // Grand Total = unique total_payable per fee (fee amount counted once per fee_name+fees_type combo)
        $grandTotal = $payments
            ->groupBy(fn ($p) => $p->fees_type . '||' . $p->fee_name)
            ->sum(fn ($group) => (float) $group->first()->total_payable);

        // Grand Paid = sum of every individual payment made
        $grandPaid = $payments->sum(fn ($p) => (float) $p->type_amount);

        // Grand Due = what's still owed overall
        $grandDue = max($grandTotal - $grandPaid, 0);

        $pdf = Pdf::loadView('exports.payment_report_pdf', [
            'school'      => $school,
            'student'     => $student,
            'payments'    => $payments,
            'grandTotal'  => $grandTotal,
            'grandPaid'   => $grandPaid,
            'grandDue'    => $grandDue,
            'generatedAt' => Carbon::now()->format('j-F-Y'),
        ])->setPaper('a4', 'landscape');

        $filename = 'payment_report_' . $student->student_id_number . '_' . Carbon::now()->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Download a Payment Slip PDF for a specific student with date range.
     */
    public function downloadSlipPdf(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:admission_students,id',
            'from_date'  => 'nullable|date',
            'to_date'    => 'nullable|date|after_or_equal:from_date',
        ]);

        $school = $this->getSchool();

        $student = AdmissionStudent::with([
            'schoolClass',
            'schoolGroup',
            'schoolSection',
            'schoolSession',
        ])->findOrFail($request->student_id);

        $payments = SchoolPayment::where('school_id', $school->id)
            ->where('admission_student_id', $request->student_id)
            ->when($request->from_date, fn($q) => $q->whereDate('pay_date', '>=', $request->from_date))
            ->when($request->to_date, fn($q) => $q->whereDate('pay_date', '<=', $request->to_date))
            ->orderBy('pay_date', 'asc')
            ->get();

        $duration = ($request->from_date ? Carbon::parse($request->from_date)->format('j-F-Y') : 'N/A')
            . ' To ' . ($request->to_date ? Carbon::parse($request->to_date)->format('j-F-Y') : 'N/A');

        $grandTotal = $payments
            ->groupBy(fn ($p) => $p->fees_type . '||' . $p->fee_name)
            ->sum(fn ($group) => (float) $group->first()->total_payable);

        $grandPaid = $payments->sum(fn ($p) => (float) $p->type_amount);
        $grandDue = max($grandTotal - $grandPaid, 0);

        // Map fee due dates from school_student_fees for "Pay Date" column
        $feeDueDates = [];
        if ($payments->isNotEmpty()) {
            $studentFees = SchoolStudentFee::where('school_id', $school->id)
                ->where('student_id', $request->student_id)
                ->whereIn('fee_type_name', $payments->pluck('fees_type')->unique())
                ->select('fee_type_name', 'fee_name', 'pay_date')
                ->get();
            foreach ($studentFees as $sf) {
                $feeDueDates[$sf->fee_type_name . '||' . $sf->fee_name] = $sf->pay_date;
            }
        }

        $pdf = Pdf::loadView('exports.payment_slip_pdf', [
            'school'      => $school,
            'student'     => $student,
            'payments'    => $payments,
            'duration'    => $duration,
            'printDate'   => Carbon::now()->format('j-F-Y h:i A'),
            'grandTotal'  => $grandTotal,
            'grandPaid'   => $grandPaid,
            'grandDue'    => $grandDue,
            'feeDueDates' => $feeDueDates,
        ])->setPaper('a4', 'portrait');

        return $pdf->download("payment_slip_{$student->student_id_number}_" . Carbon::now()->format('Ymd') . ".pdf");
    }

    /**
     * Get per-month payment status grid for a student within their session.
     */
    public function paymentMonths($studentId)
    {
        $school = $this->getSchool();

        $student = AdmissionStudent::with([
            'schoolClass', 'schoolGroup', 'schoolSection', 'schoolSession'
        ])->findOrFail($studentId);

        $sessionId = $student->session
            ?? $student->school_session?->id;
        $session = SchoolSession::where('school_id', $school->id)->find($sessionId);

        $startDate = $session?->start_date ? Carbon::parse($session->start_date) : null;
        $endDate   = $session?->end_date   ? Carbon::parse($session->end_date)   : null;

        $months = [];
        if ($startDate && $endDate) {
            $now = Carbon::now();
            $cursor = $startDate->copy()->startOfMonth();

            while ($cursor->lte($endDate)) {
                $yearMonth = $cursor->format('Y-m');

                $fees = SchoolStudentFee::where('school_id', $school->id)
                    ->where('student_id', $studentId)
                    ->whereYear('pay_date', $cursor->year)
                    ->whereMonth('pay_date', $cursor->month)
                    ->get();

                $totalAmount = 0;
                $totalPaid   = 0;

                foreach ($fees as $fee) {
                    $feeAmount = (float) $fee->payable_amount;
                    // Prefer FK-based payment sum (exact match)
                    $paid = (float) SchoolPayment::where('school_student_fee_id', $fee->id)
                        ->sum('type_amount');
                    // Fallback for payments that pre-date the FK column
                    if ($paid == 0) {
                        $paid = (float) SchoolPayment::where('school_id', $school->id)
                            ->whereNull('school_student_fee_id')
                            ->where('admission_student_id', $studentId)
                            ->where('fees_type', $fee->fee_type_name)
                            ->where('fee_name', $fee->fee_name)
                            ->whereYear('pay_date', $cursor->year)
                            ->whereMonth('pay_date', $cursor->month)
                            ->sum('type_amount');
                    }
                    $totalAmount += $feeAmount;
                    $totalPaid   += $paid;
                }

                $due = max($totalAmount - $totalPaid, 0);
                $isFuture = $cursor->isFuture();
                $isCurrent = $yearMonth === $now->format('Y-m');
                $isPast = $cursor->isPast() && !$isCurrent;

                if ($totalAmount == 0) {
                    $status = match (true) {
                        $isCurrent => 'current',
                        $isFuture  => 'locked',
                        default    => 'no_fees',
                    };
                } elseif ($totalPaid >= $totalAmount) {
                    $status = match (true) {
                        $isFuture => 'advance',
                        default   => 'paid',
                    };
                } elseif ($totalPaid > 0) {
                    $status = 'partial';
                } elseif ($isPast) {
                    $status = 'overdue';
                } else {
                    $status = 'current';
                }

                $months[] = [
                    'year_month' => $yearMonth,
                    'month_name' => $cursor->format('F'),
                    'year'       => (int) $cursor->year,
                    'status'     => $status,
                    'total'      => $totalAmount,
                    'paid'       => $totalPaid,
                    'due'        => $due,
                    'is_current' => $isCurrent,
                    'is_future'  => $isFuture,
                    'is_past'    => $isPast,
                ];

                $cursor->addMonth();
            }
        }

        $hasDue = collect($months)->sum('due') > 0;

        return response()->json([
            'months'      => $months,
            'has_due'     => $hasDue,
            'all_cleared' => !$hasDue,
            'student'     => [
                'id'      => $student->id,
                'name'    => $student->student_name,
                'id_no'   => $student->student_id_number,
                'class'   => $student->schoolClass?->class_name ?? $student->class_name,
                'group'   => $student->schoolGroup?->group_name ?? $student->group_name,
                'section' => $student->schoolSection?->section_name ?? $student->section_name,
                'session' => $student->schoolSession?->session_year ?? $student->session_year,
            ],
        ]);
    }

    /**
     * Store an advance payment for a specific future month.
     * Creates a school_student_fees record if one doesn't exist yet.
     */
    public function storeAdvance(Request $request)
    {
        $school = $this->getSchool();

        $validated = $request->validate([
            'admission_student_id' => 'required|exists:admission_students,id',
            'fees_type'            => 'required|string|in:Food,Tuition',
            'fee_name'             => 'required|string',
            'amount'               => 'required|numeric|min:1',
            'for_month'            => 'required|regex:/^\d{4}-\d{2}$/',
            'pay_date'             => 'required|date',
            'pay_method'           => 'required|string',
        ]);

        $studentId = $validated['admission_student_id'];
        $forMonth  = $validated['for_month'];
        $amount    = (float) $validated['amount'];

        // Ensure for_month is within session and is future
        $student = AdmissionStudent::findOrFail($studentId);

        // Modified on 2026-07-09: Prevent collecting advance payments for inactive students
        if ($student->status === 'Inactive') {
            return response()->json(['message' => 'Cannot collect advance payment for inactive students.'], 403);
        }

        $sessionId = $student->session;
        $session = SchoolSession::where('school_id', $school->id)->find($sessionId);

        if (!$session || !$session->start_date || !$session->end_date) {
            return response()->json(['message' => 'Student session not configured.'], 400);
        }

        $forDate = Carbon::parse($forMonth . '-01');
        if ($forDate->lte(Carbon::now()->startOfMonth())) {
            return response()->json(['message' => 'Advance payment allowed for future months only.'], 400);
        }
        if ($forDate->lt(Carbon::parse($session->start_date)->startOfMonth()) ||
            $forDate->gt(Carbon::parse($session->end_date)->endOfMonth())) {
            return response()->json(['message' => 'Month is outside the student\'s session range.'], 400);
        }

        $dueDay = min((int) ($session->fees_due ?? $forDate->day), 28);
        $feePayDate = $forMonth . '-' . str_pad($dueDay, 2, '0', STR_PAD_LEFT);

        // Check if a fee record already exists for this month+type+name
        $existingFee = SchoolStudentFee::where('school_id', $school->id)
            ->where('student_id', $studentId)
            ->where('fee_type_name', $validated['fees_type'])
            ->where('fee_name', $validated['fee_name'])
            ->whereYear('pay_date', $forDate->year)
            ->whereMonth('pay_date', $forDate->month)
            ->first();

        if ($existingFee) {
            $feeRecord = $existingFee;
        } else {
            // Find the template to determine the amount
            $template = SchoolFeeTemplate::where('school_id', $school->id)
                ->where('fee_type_name', $validated['fees_type'])
                ->where('fee_name', $validated['fee_name'])
                ->first();

            $feeAmount = $template ? (float) $template->amount : $amount;

            $feeRecord = SchoolStudentFee::create([
                'school_id'       => $school->id,
                'student_id'      => $studentId,
                'fee_template_id' => $template?->id,
                'fee_type_name'   => $validated['fees_type'],
                'fee_name'        => $validated['fee_name'],
                'base_amount'     => $feeAmount,
                'payable_amount'  => $feeAmount,
                'due_amount'      => $feeAmount,
                'pay_date'        => $feePayDate,
                'status'          => 'unpaid',
            ]);
        }

        // Check if this fee is already fully paid
        $alreadyPaid = (float) SchoolPayment::where('school_id', $school->id)
            ->where('admission_student_id', $studentId)
            ->where('fees_type', $validated['fees_type'])
            ->where('fee_name', $validated['fee_name'])
            ->sum('type_amount');

        $feeAmount = (float) $feeRecord->base_amount;
        $remainingDue = max($feeAmount - $alreadyPaid, 0);

        if ($amount > $remainingDue) {
            return response()->json([
                'message' => "Amount ৳{$amount} exceeds remaining due ৳{$remainingDue} for this fee.",
                'remaining_due' => $remainingDue,
            ], 400);
        }

        $newTotalPaid = $alreadyPaid + $amount;
        $paymentStatus = $newTotalPaid >= $feeAmount ? 'paid' : 'partial';

        $payment = DB::transaction(function () use ($school, $validated, $studentId, $feeRecord, $feeAmount, $newTotalPaid, $amount, $paymentStatus) {
            $payment = SchoolPayment::create([
                'school_id'             => $school->id,
                'school_student_fee_id' => $feeRecord->id,
                'admission_student_id'  => $studentId,
                'fees_type'             => $validated['fees_type'],
                'fee_name'              => $validated['fee_name'],
                'total_payable'         => $feeAmount,
                'type_amount'           => $amount,
                'payable_due'           => max($feeAmount - $newTotalPaid, 0),
                'total_amount'          => $amount,
                'total_due'             => max($feeAmount - $newTotalPaid, 0),
                'pay_date'              => $validated['pay_date'],
                'for_month'             => $validated['for_month'],
                'pay_method'            => $validated['pay_method'],
                'status'                => $paymentStatus,
            ]);

            // Sync fee record status using unified helper
            $this->syncFeeStatus($feeRecord);

            // Cash In to the internal System Cash Balance (immutable ledger)
            app(AccountService::class)->cashIn(
                $school->id,
                $amount,
                'Advance Collection',
                $payment->id,
                [
                    'before_discount' => $feeAmount,
                    'after_discount'  => $feeAmount,
                    'remarks'         => ($validated['fees_type'] ?? '') . ' - ' . ($validated['fee_name'] ?? '') . ' (Advance) | Student #' . $studentId,
                ]
            );

            return $payment;
        });

        return response()->json([
            'status'  => 'success',
            'message' => 'Advance payment recorded successfully.',
            'data'    => $payment->load('student'),
        ], 201);
    }

    /**
     * Download a Payment Slip Excel for a specific student with date range.
     */
    public function downloadSlipExcel(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:admission_students,id',
            'from_date'  => 'nullable|date',
            'to_date'    => 'nullable|date|after_or_equal:from_date',
        ]);

        $school = $this->getSchool();

        $student = AdmissionStudent::with([
            'schoolClass',
            'schoolGroup',
            'schoolSection',
            'schoolSession',
        ])->findOrFail($request->student_id);

        $payments = SchoolPayment::where('school_id', $school->id)
            ->where('admission_student_id', $request->student_id)
            ->when($request->from_date, fn($q) => $q->whereDate('pay_date', '>=', $request->from_date))
            ->when($request->to_date, fn($q) => $q->whereDate('pay_date', '<=', $request->to_date))
            ->orderBy('pay_date', 'asc')
            ->get();

        $duration = ($request->from_date ? Carbon::parse($request->from_date)->format('j-F-Y') : 'N/A')
            . ' To ' . ($request->to_date ? Carbon::parse($request->to_date)->format('j-F-Y') : 'N/A');

        return Excel::download(
            new PaymentSlipExport($payments, $student, $school, $duration),
            "payment_slip_{$student->student_id_number}_" . Carbon::now()->format('Ymd') . ".xlsx"
        );
    }
}