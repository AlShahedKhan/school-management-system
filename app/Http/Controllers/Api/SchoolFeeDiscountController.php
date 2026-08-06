<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSchoolFeeDiscountRequest;
use App\Http\Requests\UpdateSchoolFeeDiscountRequest;
use App\Models\AdmissionStudent;
use App\Models\SchoolFeeDiscount;
use App\Models\School;
use App\Models\SchoolPayment;
use App\Models\SchoolStudentFee;
use App\Models\SchoolFeeTemplate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SchoolFeeDiscountController extends Controller
{
    private function getSchool($user)
    {
        return School::where('user_id', $user->id)->first();
    }

    private function resolveStudentIds(int $schoolId, array $validated): array
    {
        if ($validated['student_scope'] === 'all') {
            $query = AdmissionStudent::where('school_id', $schoolId)
                ->where('status', '!=', 'Inactive')
                ->where('class_id', $validated['class_id'])
                ->where('session_id', $validated['session_id']);

            if (!empty($validated['group_id'])) {
                $query->where('group_id', $validated['group_id']);
            }
            if (!empty($validated['section_id'])) {
                $query->where('section_id', $validated['section_id']);
            }

            return $query->pluck('id')->all();
        }

        return $validated['student_ids'] ?? [];
    }

    private function applyDiscountToPayments(int $schoolId, int $studentId, string $feeTypeName, string $feeName, float $afterDiscount, ?SchoolFeeTemplate $feeType = null, bool $respectExistingBalance = false): void
    {
        $studentFees = SchoolStudentFee::where('school_id', $schoolId)
            ->where('student_id', $studentId)
            ->where('fee_type_name', $feeTypeName)
            ->where('fee_name', $feeName)
            ->get();

        $applicableFeeIds = [];
        foreach ($studentFees as $studentFee) {
            if (!$this->shouldApplyDiscountToFee($studentFee, $feeType, $respectExistingBalance)) {
                continue;
            }

            $applicableFeeIds[] = $studentFee->id;
        }

        if (empty($applicableFeeIds)) {
            return;
        }

        $payments = SchoolPayment::where('school_id', $schoolId)
            ->where('admission_student_id', $studentId)
            ->where('fees_type', $feeTypeName)
            ->where('fee_name', $feeName)
            ->where('status', '!=', 'paid')
            ->where(function ($query) use ($applicableFeeIds) {
                $query->whereIn('school_student_fee_id', $applicableFeeIds)
                    ->orWhereNull('school_student_fee_id');
            })
            ->get();

        foreach ($payments as $payment) {
            if ($payment->school_student_fee_id === null) {
                $matchedFee = SchoolStudentFee::where('school_id', $schoolId)
                    ->where('student_id', $studentId)
                    ->where('fee_type_name', $feeTypeName)
                    ->where('fee_name', $feeName)
                    ->orderByDesc('id')
                    ->first();

                if (!$matchedFee || !$this->shouldApplyDiscountToFee($matchedFee, $feeType, $respectExistingBalance)) {
                    continue;
                }
            }

            $alreadyPaid  = (float) $payment->total_amount;
            $newPayable   = $afterDiscount;
            $newDue       = max($newPayable - $alreadyPaid, 0);

            if ($alreadyPaid >= $newPayable && $newPayable > 0) {
                $newStatus = 'paid';
            } elseif ($alreadyPaid > 0 && $newDue > 0) {
                $newStatus = 'partial';
            } else {
                $newStatus = 'unpaid';
            }

            $payment->update([
                'total_payable' => $newPayable,
                'payable_due'   => $newDue,
                'total_due'     => $newDue,
                'status'        => $newStatus,
            ]);
        }

        $this->applyDiscountToStudentFees($schoolId, $studentId, $feeTypeName, $feeName, $afterDiscount, $feeType, $respectExistingBalance);
    }

    private function applyDiscountToStudentFees(int $schoolId, int $studentId, string $feeTypeName, string $feeName, float $afterDiscount, ?SchoolFeeTemplate $feeType = null, bool $respectExistingBalance = false): void
    {
        $studentFees = SchoolStudentFee::where('school_id', $schoolId)
            ->where('student_id', $studentId)
            ->where('fee_type_name', $feeTypeName)
            ->where('fee_name', $feeName)
            ->get();

        foreach ($studentFees as $studentFee) {
            if (!$this->shouldApplyDiscountToFee($studentFee, $feeType, $respectExistingBalance)) {
                continue;
            }

            $baseAmount   = (float) $studentFee->base_amount;
            $discount     = max($baseAmount - $afterDiscount, 0);
            $newDue       = max($afterDiscount - (float) $studentFee->paid_amount, 0);

            $studentFee->update([
                'discount_amount' => round($discount, 2),
                'payable_amount'  => round($afterDiscount, 2),
                'due_amount'      => round($newDue, 2),
            ]);
        }
    }

    private function shouldApplyDiscountToFee(SchoolStudentFee $studentFee, ?SchoolFeeTemplate $feeType, bool $respectExistingBalance): bool
    {
        // Students who already paid must never have invoices/history modified.
        $paidAmount = (float) SchoolPayment::where('school_student_fee_id', $studentFee->id)
            ->sum('type_amount');

        if ($paidAmount > 0) {
            return false;
        }

        $feeType = $feeType ?: ($studentFee->feeTemplate ?: null);
        $feeTypeName = strtolower((string) ($feeType ? $feeType->fee_type_name : $studentFee->fee_type_name));
        $frequency = strtolower((string) ($feeType ? $feeType->frequency : null));

        // Tuition / Food (or any monthly frequency):
        // previous months never change; current month applies only when unpaid
        // (paid_amount > 0 already returned above); future months apply.
        $isMonthly = in_array($feeTypeName, ['tuition', 'food']) || $frequency === 'monthly';

        if ($isMonthly) {
            $billingMonth = $this->feeBillingMonth($studentFee);

            if (!$billingMonth) {
                return true;
            }

            return $billingMonth->copy()->startOfMonth()->gte(Carbon::now()->startOfMonth());
        }

        // One-time types (Admission, Promote, Session, Exam) apply immediately.
        if ($respectExistingBalance) {
            return true;
        }

        return true;
    }

    private function feeBillingMonth(SchoolStudentFee $studentFee): ?Carbon
    {
        if ($studentFee->generation_period && preg_match('/^\d{4}-\d{2}$/', $studentFee->generation_period)) {
            return Carbon::createFromFormat('Y-m', $studentFee->generation_period);
        }

        if ($studentFee->pay_date) {
            return Carbon::parse($studentFee->pay_date);
        }

        return null;
    }

    public function index(Request $request)
    {
        try {
            $school = $this->getSchool($request->user());

            if (!$school) {
                return response()->json(['error' => 'School not found'], 404);
            }

            $query = SchoolFeeDiscount::with([
                'schoolClass',
                'schoolSession',
                'schoolGroup',
                'schoolSection',
                'student',
                'feeType'
            ])->where('school_id', $school->id);

            if ($request->filled('class_id')) {
                $query->where('class_id', $request->class_id);
            }
            if ($request->filled('group_id')) {
                $query->where('group_id', $request->group_id);
            }
            if ($request->filled('section_id')) {
                $query->where('section_id', $request->section_id);
            }
            if ($request->filled('session_id')) {
                $query->where('session_id', $request->session_id);
            }

            if ($request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->whereHas('student', function ($sq) use ($search) {
                        $sq->where('student_name', 'like', "%{$search}%")
                            ->orWhere('student_id_number', 'like', "%{$search}%");
                    })->orWhereHas('feeType', function ($fq) use ($search) {
                        $fq->where('fee_type_name', 'like', "%{$search}%");
                    });
                });
            }

            $result = $request->boolean('all')
                ? ['data' => $query->latest()->get()]
                : $query->latest()->paginate(10);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to load discounts.'], 500);
        }
    }

    private function computeDiscount(string $discountType, float $discountValue, float $before): array
    {
        $discountAmount = $discountType === 'Percentage'
            ? $before * $discountValue / 100
            : $discountValue;
        $after = max($before - $discountAmount, 0);

        return [
            'before_discount' => round($before, 2),
            'discount_amount' => round($discountAmount, 2),
            'after_discount'  => round($after, 2),
        ];
    }

    public function store(StoreSchoolFeeDiscountRequest $request)
    {
        try {
            $school = $this->getSchool($request->user());

            if (!$school) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $validated = $request->validated();
            $scope = $validated['discount_scope'] ?? 'session';

            $feeDiscounts = DB::transaction(function () use ($validated, $scope, $school) {
                $created = [];
                $studentIds = $this->resolveStudentIds($school->id, $validated);

                $feeType = $validated['fee_type_id']
                    ? SchoolFeeTemplate::where('id', $validated['fee_type_id'])->first()
                    : null;

                if ($scope === 'exam') {
                    $amounts = $feeType
                        ? $this->computeDiscount(
                            $validated['discount_type'],
                            (float) $validated['discount_value'],
                            (float) $feeType->amount
                        )
                        : [
                            'before_discount' => null,
                            'discount_amount' => null,
                            'after_discount'  => null,
                        ];

                    foreach ($studentIds as $studentId) {
                        $created[] = SchoolFeeDiscount::create(array_merge($validated, [
                            'school_id'       => $school->id,
                            'student_id'      => $studentId,
                            'discount_scope'  => 'exam',
                            'minimum_grade'   => $validated['minimum_grade'] ?? null,
                            'fee_type_id'     => $feeType ? $feeType->id : null,
                            'fee_name'        => $feeType ? $feeType->fee_name : null,
                            'before_discount' => $amounts['before_discount'],
                            'discount_amount' => $amounts['discount_amount'],
                            'after_discount'  => $amounts['after_discount'],
                        ]));
                    }

                    return $created;
                }

                $feeTypeIds = $validated['fee_type_id'] ? [$validated['fee_type_id']] : [];
                foreach ($feeTypeIds as $feeTypeId) {
                    $feeType = SchoolFeeTemplate::where('id', $feeTypeId)->first();
                    if (!$feeType) {
                        continue;
                    }

                    $amounts = $this->computeDiscount(
                        $validated['discount_type'],
                        (float) $validated['discount_value'],
                        (float) $feeType->amount
                    );

                    foreach ($studentIds as $studentId) {
                        $discount = SchoolFeeDiscount::create(array_merge($validated, [
                            'school_id'       => $school->id,
                            'student_id'      => $studentId,
                            'discount_scope'  => 'session',
                            'minimum_grade'   => null,
                            'fee_type_id'     => $feeType->id,
                            'fee_name'        => $feeType->fee_name,
                            'before_discount' => $amounts['before_discount'],
                            'discount_amount' => $amounts['discount_amount'],
                            'after_discount'  => $amounts['after_discount'],
                        ]));
                        $created[] = $discount;

                        $this->applyDiscountToPayments(
                            $school->id,
                            $studentId,
                            $feeType->fee_type_name,
                            $feeType->fee_name,
                            (float) $amounts['after_discount'],
                            $feeType,
                            true
                        );
                    }
                }

                return $created;
            });

            return response()->json(['status' => 'success', 'data' => $feeDiscounts], 201);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to create discount.'], 500);
        }
    }

    public function show($id)
    {
        try {
            $discount = SchoolFeeDiscount::with([
                'student',
                'feeType',
                'schoolClass',
                'schoolSession',
                'schoolGroup',
                'schoolSection'
            ])->findOrFail($id);

            return response()->json($discount);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['status' => 'error', 'message' => 'Discount not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to fetch discount.'], 500);
        }
    }

    public function update(UpdateSchoolFeeDiscountRequest $request, $id)
    {
        try {
            $school = $this->getSchool($request->user());

            if (!$school) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $discount = SchoolFeeDiscount::where('school_id', $school->id)->findOrFail($id);
            $validated = $request->validated();
            $scope = $validated['discount_scope'] ?? 'session';

            DB::transaction(function () use ($discount, $validated, $scope, $school) {
                $studentIds = $this->resolveStudentIds($school->id, $validated);
                $firstId = reset($studentIds);

                $examFeeType = $validated['fee_type_id']
                    ? SchoolFeeTemplate::where('id', $validated['fee_type_id'])->first()
                    : null;

                if ($scope === 'exam') {
                    $amounts = $examFeeType
                        ? $this->computeDiscount(
                            $validated['discount_type'],
                            (float) $validated['discount_value'],
                            (float) $examFeeType->amount
                        )
                        : [
                            'before_discount' => null,
                            'discount_amount' => null,
                            'after_discount'  => null,
                        ];

                    $discount->update(array_merge($validated, [
                        'student_id'      => $firstId ?: $discount->student_id,
                        'discount_scope'  => 'exam',
                        'minimum_grade'   => $validated['minimum_grade'] ?? null,
                        'fee_type_id'     => $examFeeType ? $examFeeType->id : null,
                        'fee_name'        => $examFeeType ? $examFeeType->fee_name : null,
                        'before_discount' => $amounts['before_discount'],
                        'discount_amount' => $amounts['discount_amount'],
                        'after_discount'  => $amounts['after_discount'],
                    ]));

                    $primaryStudentId = $firstId ?: $discount->student_id;

                    foreach ($studentIds as $studentId) {
                        if ((string) $studentId === (string) $primaryStudentId) {
                            continue;
                        }

                        $existing = SchoolFeeDiscount::where('school_id', $school->id)
                            ->where('discount_scope', 'exam')
                            ->where('session_id', $validated['session_id'])
                            ->where('student_id', $studentId)
                            ->first();

                        if (!$existing) {
                            SchoolFeeDiscount::create(array_merge($validated, [
                                'school_id'       => $school->id,
                                'student_id'      => $studentId,
                                'discount_scope'  => 'exam',
                                'minimum_grade'   => $validated['minimum_grade'] ?? null,
                                'fee_type_id'     => $examFeeType ? $examFeeType->id : null,
                                'fee_name'        => $examFeeType ? $examFeeType->fee_name : null,
                                'before_discount' => $amounts['before_discount'],
                                'discount_amount' => $amounts['discount_amount'],
                                'after_discount'  => $amounts['after_discount'],
                            ]));
                        }
                    }

                    return;
                }

                $feeTypeIds = $validated['fee_type_id'] ? [$validated['fee_type_id']] : [];
                foreach ($feeTypeIds as $index => $feeTypeId) {
                    $feeType = SchoolFeeTemplate::where('id', $feeTypeId)->first();
                    if (!$feeType) {
                        continue;
                    }

                    $amounts = $this->computeDiscount(
                        $validated['discount_type'],
                        (float) $validated['discount_value'],
                        (float) $feeType->amount
                    );

                    $isFirst = $index === 0;
                    foreach ($studentIds as $studentId) {
                        if ($isFirst && (string) $studentId === (string) ($firstId ?: $discount->student_id)) {
                            $discount->update(array_merge($validated, [
                                'student_id'      => $firstId ?: $discount->student_id,
                                'discount_scope'  => 'session',
                                'minimum_grade'   => null,
                                'fee_type_id'     => $feeType->id,
                                'fee_name'        => $feeType->fee_name,
                                'before_discount' => $amounts['before_discount'],
                                'discount_amount' => $amounts['discount_amount'],
                                'after_discount'  => $amounts['after_discount'],
                            ]));
                        } else {
                            $existing = SchoolFeeDiscount::where('school_id', $school->id)
                                ->where('discount_scope', 'session')
                                ->where('session_id', $validated['session_id'])
                                ->where('fee_type_id', $feeType->id)
                                ->where('student_id', $studentId)
                                ->where('id', '!=', $discount->id)
                                ->first();

                            if (!$existing) {
                                SchoolFeeDiscount::create(array_merge($validated, [
                                    'school_id'       => $school->id,
                                    'student_id'      => $studentId,
                                    'discount_scope'  => 'session',
                                    'minimum_grade'   => null,
                                    'fee_type_id'     => $feeType->id,
                                    'fee_name'        => $feeType->fee_name,
                                    'before_discount' => $amounts['before_discount'],
                                    'discount_amount' => $amounts['discount_amount'],
                                    'after_discount'  => $amounts['after_discount'],
                                ]));
                            }
                        }

                        $this->applyDiscountToPayments(
                            $school->id,
                            $studentId,
                            $feeType->fee_type_name,
                            $feeType->fee_name,
                            (float) $amounts['after_discount'],
                            $feeType,
                            true
                        );
                    }
                }
            });

            return response()->json(['status' => 'success', 'data' => $discount->fresh()]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['status' => 'error', 'message' => 'Discount not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to update discount.'], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $school = $this->getSchool($request->user());

            if (!$school) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $discount = SchoolFeeDiscount::where('school_id', $school->id)->findOrFail($id);

            DB::transaction(function () use ($discount) {
                $discount->delete();
            });

            return response()->json(['status' => 'success']);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['status' => 'error', 'message' => 'Discount not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to delete discount.'], 500);
        }
    }
}
