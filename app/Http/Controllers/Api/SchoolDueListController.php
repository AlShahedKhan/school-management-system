<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\SchoolPayment;
use App\Models\SchoolStudentFee;
use App\Services\FeeStatusSyncService;
use App\Services\AccountService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SchoolDueListController extends Controller
{
    public function index(Request $request)
    {
        $school = School::where('user_id', Auth::id())->first();
        $schoolId = $school ? $school->id : Auth::user()->school_id;

        if (!$schoolId) {
            return response()->json(['data' => [], 'message' => 'Unauthorized'], 403);
        }

        app(FeeStatusSyncService::class)->syncPending($schoolId);

        $search = $request->query('search');
        $all = $request->query('all');
        $today = Carbon::today();

        $query = SchoolStudentFee::with([
            'student.schoolClass',
            'student.schoolGroup',
            'student.schoolSection',
            'student.schoolSession'
        ])
            ->where('school_id', $schoolId)
            // Modified on 2026-07-09: Exclude Inactive students from dues list
            ->whereHas('student', function ($sub) {
                $sub->where('status', '!=', 'Inactive');
            })
            ->where('pay_date', '<', $today)
            ->whereNotIn('status', ['paid', 'advance', 'advance_partial']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('student', function ($sub) use ($search) {
                    $sub->where('student_name', 'like', "%{$search}%")
                        ->orWhere('student_id_number', 'like', "%{$search}%");
                })->orWhere('fee_type_name', 'like', "%{$search}%");
            });
        }

        // Filter by the student's placement (values come from the shared filter modal).
        if ($request->filled('class_id')) {
            $query->whereHas('student', fn ($q) => $q->where('class_id', $request->class_id));
        }
        if ($request->filled('group_id')) {
            $query->whereHas('student', fn ($q) => $q->where('group_id', $request->group_id));
        }
        if ($request->filled('section_id')) {
            $query->whereHas('student', fn ($q) => $q->where('section_id', $request->section_id));
        }
        if ($request->filled('session_id')) {
            $query->whereHas('student', fn ($q) => $q->where('session_id', $request->session_id));
        }
        if ($request->filled('student')) {
            $query->where('student_id', $request->student);
        }

        // Status is derived (paid total + due date); compute it in SQL so the
        // filter applies before pagination instead of post-filtering one page.
        if ($request->filled('status')) {
            $paidExpr = "(SELECT COALESCE(SUM(type_amount), 0) FROM school_payments"
                . " WHERE school_student_fee_id = school_student_fees.id)";
            $effective = "COALESCE(NULLIF(school_student_fees.payable_amount, 0), school_student_fees.base_amount)";
            $statusExpr = "CASE"
                . " WHEN {$paidExpr} >= {$effective} THEN 'paid'"
                . " WHEN DATE_FORMAT(school_student_fees.pay_date, '%Y-%m') < DATE_FORMAT(CURDATE(), '%Y-%m') AND {$paidExpr} > 0 THEN 'over_due_partial'"
                . " WHEN DATE_FORMAT(school_student_fees.pay_date, '%Y-%m') < DATE_FORMAT(CURDATE(), '%Y-%m') THEN 'over_due'"
                . " WHEN school_student_fees.pay_date < CURDATE() AND {$paidExpr} > 0 THEN 'due_partial'"
                . " WHEN school_student_fees.pay_date < CURDATE() THEN 'due'"
                . " WHEN {$paidExpr} > 0 THEN 'partial_paid'"
                . " ELSE 'pending' END";

            $query->select('school_student_fees.*')
                ->selectRaw("{$statusExpr} AS fee_status")
                ->havingRaw('fee_status = ?', [$request->status]);
        }

        $query->orderBy('pay_date', 'desc');

        if ($all) {
            $fees = $query->get();
            $result = $this->transformFees($fees, $today);
            return response()->json($result);
        }

        $paginated = $query->paginate(10);
        $paginated->getCollection()->transform(function ($fee) use ($today) {
            return $this->mapFeeRecord($fee, $today);
        });

        // Filter out fully paid
        $paginated->setCollection(
            $paginated->getCollection()->filter(fn($f) => $f->remaining_due > 0)->values()
        );

        return response()->json($paginated);
    }

    private function transformFees($fees, Carbon $today)
    {
        return $fees->map(function ($fee) use ($today) {
            return $this->mapFeeRecord($fee, $today);
        })->filter(fn($f) => $f->remaining_due > 0)->values();
    }

    private function mapFeeRecord($fee, Carbon $today)
    {
        $paid = (float) SchoolPayment::where('school_student_fee_id', $fee->id)
            ->sum('type_amount');

        // Amount comes from the stored fee record (school_student_fees),
        // which holds the discounted payable after generation/propagation.
        $amount = (float) ($fee->payable_amount ?: $fee->base_amount);
        $remainingDue = max($amount - $paid, 0);

        $payDate = $fee->pay_date ? Carbon::parse($fee->pay_date) : null;
        $isPastMonth = $payDate && $payDate->copy()->startOfMonth()->lt($today->copy()->startOfMonth());
        $isSameMonthDatePassed = $payDate && !$isPastMonth && $payDate->lt($today);

        $overduePenalty = $isPastMonth ? $remainingDue : 0;
        $hasAlert = $overduePenalty > 0;

        $displayStatus = match (true) {
            $remainingDue <= 0                               => 'paid',
            $isPastMonth && $paid > 0                        => 'over_due_partial',
            $isPastMonth                                     => 'over_due',
            $isSameMonthDatePassed && $paid > 0              => 'due_partial',
            $isSameMonthDatePassed                           => 'due',
            $paid > 0                                        => 'partial_paid',
            default                                          => 'pending',
        };

        $record = (object) [
            'payment_id'          => $fee->id,
            'status'              => $displayStatus,
            'display_pay_date'    => $payDate ? $payDate->format('j-F-Y') : 'N/A',
            'display_last_pay_date' => $payDate ? $payDate->format('j-F-Y') : 'N/A',
            'display_due_date'    => $fee->due_date ? Carbon::parse($fee->due_date)->format('j-F-Y') : 'N/A',
            'pay_method'          => '—',
            'total_payable'       => $amount,
            'total_amount'        => $paid,
            'total_due'           => $isPastMonth ? 0 : $remainingDue,
            'overdue_penalty'     => $overduePenalty,
            'has_alert_penalty'   => $hasAlert,
            'final_payable_total' => $remainingDue,
            'remaining_due'       => $remainingDue,
            'fees_type'           => $fee->fee_type_name,
            'fee_name'            => $fee->fee_name,
            'pay_date'            => $fee->pay_date,
            'student_id_number'   => $fee->student->student_id_number ?? '---',
            'student_name'        => $fee->student->student_name ?? 'Unknown',
            'class'               => $fee->student->schoolClass->class_name ?? 'N/A',
            'group'               => $fee->student->schoolGroup->group_name ?? 'N/A',
            'section'             => $fee->student->schoolSection->section_name ?? 'N/A',
            'session'             => $fee->student->schoolSession->session_year ?? 'N/A',
        ];

        return $record;
    }

    public function pay(Request $request)
    {
        $school = School::where('user_id', Auth::id())->first();
        $schoolId = $school ? $school->id : Auth::user()->school_id;

        $validated = $request->validate([
            'school_student_fee_id' => 'required|exists:school_student_fees,id',
            'type_amount'          => 'required|numeric|min:0.01',
            'pay_method'           => 'required|string',
            'pay_date'             => 'required|date',
        ]);

        $fee = SchoolStudentFee::where('id', $validated['school_student_fee_id'])
            ->where('school_id', $schoolId)
            ->firstOrFail();

        $forMonth = $fee->pay_date ? Carbon::parse($fee->pay_date)->format('Y-m') : now()->format('Y-m');

        $alreadyPaid = (float) SchoolPayment::where('school_student_fee_id', $fee->id)
            ->sum('type_amount');

        $effectiveTotal = (float) ($fee->payable_amount ?: $fee->base_amount);

        $remainingAfter = max($effectiveTotal - ($alreadyPaid + $validated['type_amount']), 0);

        $payment = DB::transaction(function () use ($schoolId, $fee, $effectiveTotal, $remainingAfter, $forMonth, $validated) {
            $payment = SchoolPayment::create([
                'school_id'             => $schoolId,
                'school_student_fee_id' => $fee->id,
                'admission_student_id'  => $fee->student_id,
                'fees_type'             => $fee->fee_type_name,
                'fee_name'              => $fee->fee_name,
                'total_payable'         => $effectiveTotal,
                'payable_due'           => $remainingAfter,
                'status'                => 'paid',
                'total_amount'          => $validated['type_amount'],
                'total_due'             => $remainingAfter,
                'pay_type'              => 'Payable',
                'type_amount'           => $validated['type_amount'],
                'pay_date'              => $validated['pay_date'],
                'for_month'             => $forMonth,
                'pay_method'            => $validated['pay_method'],
            ]);

            app(FeeStatusSyncService::class)->syncSingle($fee);

            // Cash In to the internal System Cash Balance (immutable ledger)
            app(AccountService::class)->cashIn(
                $schoolId,
                (float) $validated['type_amount'],
                AccountService::moduleForFeeType($fee->fee_type_name),
                $payment->id,
                [
                    'before_discount' => (float) $fee->base_amount,
                    'after_discount'  => $effectiveTotal,
                    'remarks'         => ($fee->fee_type_name ?? '') . ' - ' . ($fee->fee_name ?? '') . ' | Student #' . $fee->student_id,
                ]
            );

            return $payment;
        });

        return response()->json([
            'message' => 'Payment successful',
            'status'  => $fee->fresh()->status,
        ]);
    }

    public function updatePayment(Request $request, $id)
    {
        $school = School::where('user_id', Auth::id())->first();
        $schoolId = $school ? $school->id : Auth::user()->school_id;

        $payment = SchoolPayment::where('id', $id)
            ->where('school_id', $schoolId)
            ->first();

        if (!$payment) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        $paying = (float)$request->paying_amount;
        $totalOutstanding = (float)$payment->total_due;

        $newDue = max(0, $totalOutstanding - $paying);
        $newPaidTotal = (float)$payment->total_amount + $paying;

        if ($newDue <= 0) {
            $status = 'paid';
        } elseif ($newPaidTotal > 0 && $newDue > 0) {
            $status = 'partial';
        } else {
            $status = 'unpaid';
        }

        $payment = DB::transaction(function () use ($payment, $newPaidTotal, $newDue, $status, $request, $paying, $schoolId) {
            $payment->update([
                'total_amount' => $newPaidTotal,
                'total_due' => $newDue,
                'payable_due' => $newDue,
                'status' => $status,
                'pay_method' => $request->pay_method,
                'pay_date' => $request->pay_date ?? now()->format('Y-m-d')
            ]);

            // Cash In the additional amount to the internal System Cash Balance
            if ($paying > 0) {
                app(AccountService::class)->cashIn(
                    $schoolId,
                    $paying,
                    AccountService::moduleForFeeType($payment->fees_type),
                    $payment->id,
                    [
                        'before_discount' => (float) $payment->total_payable,
                        'after_discount'  => (float) $payment->total_payable,
                        'remarks'         => ($payment->fees_type ?? '') . ' - ' . ($payment->fee_name ?? '') . ' (additional due payment) | Student #' . $payment->admission_student_id,
                    ]
                );
            }
        });

        return response()->json(['message' => 'Payment Updated Successfully']);
    }

    public function destroy($id)
    {
        return response()->json(['message' => 'Not available in the current version'], 400);
    }
}
