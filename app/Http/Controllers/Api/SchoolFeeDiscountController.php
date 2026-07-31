<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSchoolFeeDiscountRequest;
use App\Http\Requests\UpdateSchoolFeeDiscountRequest;
use App\Models\AdmissionStudent;
use App\Models\SchoolFeeDiscount;
use App\Models\School;
use App\Models\SchoolPayment;
use App\Models\SchoolFeeTemplate;
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

    private function applyDiscountToPayments(int $schoolId, int $studentId, string $feeTypeName, string $feeName, float $afterDiscount): void
    {
        $payments = SchoolPayment::where('school_id', $schoolId)
            ->where('admission_student_id', $studentId)
            ->where('fees_type', $feeTypeName)
            ->where('fee_name', $feeName)
            ->where('status', '!=', 'paid')
            ->get();

        foreach ($payments as $payment) {
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

    public function store(StoreSchoolFeeDiscountRequest $request)
    {
        try {
            $school = $this->getSchool($request->user());

            if (!$school) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $validated = $request->validated();

            $feeDiscounts = DB::transaction(function () use ($validated, $school) {
                $created = [];
                $feeType = SchoolFeeTemplate::where('id', $validated['fee_type_id'])->first();

                $studentIds = $this->resolveStudentIds($school->id, $validated);

                foreach ($studentIds as $studentId) {
                    $discount = SchoolFeeDiscount::create(array_merge($validated, [
                        'school_id' => $school->id,
                        'student_id' => $studentId,
                    ]));
                    $created[] = $discount;

                    if ($feeType) {
                        $this->applyDiscountToPayments(
                            $school->id,
                            $studentId,
                            $feeType->fee_type_name,
                            $validated['fee_name'],
                            (float) $validated['after_discount']
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

            DB::transaction(function () use ($discount, $validated, $school) {
                $studentIds = $this->resolveStudentIds($school->id, $validated);
                $firstId = reset($studentIds);

                $discount->update(array_merge($validated, [
                    'student_id' => $firstId ?: $discount->student_id,
                ]));

                $feeType = SchoolFeeTemplate::where('id', $validated['fee_type_id'])->first();
                if ($feeType) {
                    foreach ($studentIds as $studentId) {
                        $existing = SchoolFeeDiscount::where('school_id', $school->id)
                            ->where('fee_type_id', $validated['fee_type_id'])
                            ->where('session_id', $validated['session_id'])
                            ->where('student_id', $studentId)
                            ->where('id', '!=', $discount->id)
                            ->first();

                        if (!$existing) {
                            SchoolFeeDiscount::create(array_merge($validated, [
                                'school_id' => $school->id,
                                'student_id' => $studentId,
                            ]));
                        }

                        $this->applyDiscountToPayments(
                            $school->id,
                            $studentId,
                            $feeType->fee_type_name,
                            $validated['fee_name'],
                            (float) $validated['after_discount']
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
