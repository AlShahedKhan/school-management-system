<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SchoolStudentFee;
use App\Models\School;
use App\Models\SchoolPayment;
use App\Models\DiscountStudent;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SchoolStudentFeeController extends Controller
{
    private function getSchool($user)
    {
        return School::where('user_id', $user->id)->first();
    }

    public function index(Request $request)
    {
        try {
            $school = $this->getSchool($request->user());

            $query = SchoolStudentFee::with([
                'student.schoolClass',
                'student.schoolSession',
                'feeTemplate',
            ])->where('school_id', $school->id);

            if ($request->filled('class_id')) {
                $query->whereHas('student', function ($q) use ($request) {
                    $q->where('class', $request->class_id);
                });
            }

            if ($request->filled('session_id')) {
                $query->whereHas('student', function ($q) use ($request) {
                    $q->where('session', $request->session_id);
                });
            }

            if ($request->filled('fee_type_name')) {
                $query->where('fee_type_name', $request->fee_type_name);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('fee_type_name', 'like', "%{$search}%")
                        ->orWhere('fee_name', 'like', "%{$search}%")
                        ->orWhereHas('student', function ($sq) use ($search) {
                            $sq->where('student_name', 'like', "%{$search}%")
                                ->orWhere('student_id_number', 'like', "%{$search}%");
                        });
                });
            }

            if ($request->boolean('all')) {
                $results = $query->latest()->get();
            } else {
                $results = $query->latest()->paginate($request->input('per_page', 30));
            }

            $results->transform(function ($fee) {
                $totalPaid = SchoolPayment::where('admission_student_id', $fee->student_id)
                    ->where('fees_type', $fee->fee_type_name)
                    ->where('fee_name', $fee->fee_name)
                    ->sum('type_amount');

                $totalPaid = (float) $totalPaid;

                // Find best discount among all active ones for this student + fee template
                $allDiscounts = DiscountStudent::join('school_discounts', 'discount_students.discount_id', '=', 'school_discounts.id')
                    ->where('discount_students.student_id', $fee->student_id)
                    ->where('discount_students.status', 'active')
                    ->where('school_discounts.is_active', true)
                    ->where('school_discounts.fee_template_id', $fee->fee_template_id)
                    ->where('school_discounts.school_id', $fee->school_id)
                    ->select('discount_students.*', 'school_discounts.discount_type', 'school_discounts.discount_value', 'school_discounts.months')
                    ->get();
                $originalAmount = (float) $fee->amount;

                $feeMonth = $fee->pay_date ? Carbon::parse($fee->pay_date)->format('Y-m') : null;
                $bestAmount = $originalAmount;
                $hasDiscount = false;

                foreach ($allDiscounts as $d) {
                    $discountMonths = $d->months ? json_decode($d->months, true) : null;
                    if ($discountMonths && $feeMonth && !in_array($feeMonth, $discountMonths)) {
                        continue;
                    }

                    $candidate = $d->discount_type === 'Percentage'
                        ? $originalAmount - ($originalAmount * (float) $d->discount_value / 100)
                        : max($originalAmount - (float) $d->discount_value, 0);

                    if ($candidate < $bestAmount) {
                        $bestAmount = $candidate;
                        $hasDiscount = true;
                    }
                }

                $effectiveAmount = $bestAmount;
                $fee->has_discount = $hasDiscount;
                $fee->original_amount = $originalAmount;

                $fee->amount = round($effectiveAmount, 2);
                $fee->total_paid = $totalPaid;
                $fee->remaining_due = max($effectiveAmount - $totalPaid, 0);

                $payDate = $fee->pay_date ? Carbon::parse($fee->pay_date) : null;
                $today = Carbon::today();

                $fee->status = match (true) {
                    $totalPaid >= $effectiveAmount && $effectiveAmount > 0 => 'paid',
                    $payDate && $payDate->copy()->startOfMonth()->lt($today->copy()->startOfMonth()) && $totalPaid > 0 => 'over_due_partial',
                    $payDate && $payDate->copy()->startOfMonth()->lt($today->copy()->startOfMonth()) => 'over_due',
                    $payDate && $payDate->lt($today) && $totalPaid > 0 => 'due_partial',
                    $payDate && $payDate->lt($today) => 'due',
                    $totalPaid > 0 => 'partial_paid',
                    default => 'pending',
                };

                return $fee;
            });

            return response()->json($results);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching fees.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $school = $this->getSchool($request->user());

            $fee = SchoolStudentFee::with([
                'student',
                'feeTemplate',
            ])->where('school_id', $school->id)->findOrFail($id);

            return response()->json($fee);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching the fee details.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $school = $this->getSchool($request->user());
            
            $validated = $request->validate([
                'amount' => 'required|numeric|min:0',
                'pay_date' => 'required|date',
                'fee_name' => 'nullable|string|max:255',
                'status' => 'nullable|in:unpaid,paid,partial_paid,due,partial_due,over_due,partial_over_due,advance,partial_advance',
            ]);

            DB::transaction(function () use ($school, $id, $validated) {
                $fee = SchoolStudentFee::where('school_id', $school->id)->findOrFail($id);
                $validated['payable_amount'] = $validated['amount'];
                $validated['due_amount'] = $validated['amount'];
                $fee->update($validated);
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Student fee updated successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while updating the fee.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $school = $this->getSchool($request->user());
            
            DB::transaction(function () use ($school, $id) {
                $fee = SchoolStudentFee::where('school_id', $school->id)->findOrFail($id);
                $fee->delete();
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Student fee record deleted.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while deleting the fee.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getStudentFees(Request $request)
    {
        try {
            $school = $this->getSchool($request->user());

            $validated = $request->validate([
                'student_id' => 'required|exists:admission_students,id',
            ]);

            $fees = SchoolStudentFee::with(['feeTemplate'])
                ->where('school_id', $school->id)
                ->where('student_id', $validated['student_id'])
                ->latest()
                ->get();

            $fees->transform(function ($fee) {
                $totalPaid = SchoolPayment::where('admission_student_id', $fee->student_id)
                    ->where('fees_type', $fee->fee_type_name)
                    ->where('fee_name', $fee->fee_name)
                    ->sum('type_amount');

                $totalPaid = (float) $totalPaid;
                $amount = (float) $fee->amount;
                $fee->total_paid = $totalPaid;
                $fee->remaining_due = max($amount - $totalPaid, 0);

                $payDate = $fee->pay_date ? Carbon::parse($fee->pay_date) : null;
                $today = Carbon::today();

                $fee->status = match (true) {
                    $totalPaid >= $amount && $amount > 0 => 'paid',
                    $payDate && $payDate->copy()->startOfMonth()->lt($today->copy()->startOfMonth()) && $totalPaid > 0 => 'over_due_partial',
                    $payDate && $payDate->copy()->startOfMonth()->lt($today->copy()->startOfMonth()) => 'over_due',
                    $payDate && $payDate->lt($today) && $totalPaid > 0 => 'due_partial',
                    $payDate && $payDate->lt($today) => 'due',
                    $totalPaid > 0 => 'partial_paid',
                    default => 'pending',
                };

                return $fee;
            });

            return response()->json(['data' => $fees]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching the student fees.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
