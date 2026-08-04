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

    private function resolveEffectiveAmount(SchoolStudentFee $fee): float
    {
        $storedPayableAmount = (float) $fee->payable_amount;
        if ($storedPayableAmount > 0) {
            return $storedPayableAmount;
        }

        $storedBaseAmount = (float) $fee->base_amount;
        if ($storedBaseAmount > 0) {
            return $storedBaseAmount;
        }

        return 0.0;
    }

    public function index(Request $request)
    {
        try {
            $school = $this->getSchool($request->user());

            $query = SchoolStudentFee::with([
                'student.schoolClass',
                'student.schoolGroup',
                'student.schoolSection',
                'student.schoolSession',
                'feeTemplate.schoolClass',
                'feeTemplate.schoolGroup',
                'feeTemplate.schoolSection',
                'feeTemplate.schoolSession',
            ])->where('school_id', $school->id);

            if ($request->filled('class_id')) {
                $query->whereHas('student', function ($q) use ($request) {
                    $q->where('class_id', $request->class_id);
                });
            }

            if ($request->filled('group_id')) {
                $query->whereHas('student', function ($q) use ($request) {
                    $q->where('group_id', $request->group_id);
                });
            }

            if ($request->filled('section_id')) {
                $query->whereHas('student', function ($q) use ($request) {
                    $q->where('section_id', $request->section_id);
                });
            }

            if ($request->filled('session_id')) {
                $query->whereHas('student', function ($q) use ($request) {
                    $q->where('session_id', $request->session_id);
                });
            }

            if ($request->filled('fee_type_name')) {
                $query->where('fee_type_name', $request->fee_type_name);
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
                $template = $fee->feeTemplate;
                $fee->destination_class = $template?->schoolClass?->class_name;
                $fee->destination_group = $template?->schoolGroup?->group_name;
                $fee->destination_section = $template?->schoolSection?->section_name;
                $fee->destination_session = $template?->schoolSession?->session_year;

                $totalPaid = SchoolPayment::where('admission_student_id', $fee->student_id)
                    ->where('fees_type', $fee->fee_type_name)
                    ->where('fee_name', $fee->fee_name)
                    ->sum('type_amount');

                $totalPaid = (float) $totalPaid;

                $originalAmount = (float) $fee->base_amount;
                $effectiveAmount = $this->resolveEffectiveAmount($fee);
                $hasDiscount = $effectiveAmount > 0 && $originalAmount > 0 && $effectiveAmount < $originalAmount;

                $fee->has_discount = $hasDiscount;
                $fee->original_amount = $originalAmount;

                $fee->base_amount = round($effectiveAmount, 2);
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

                $fee->overdue = in_array($fee->status, ['over_due', 'over_due_partial'])
                    ? max($effectiveAmount - $totalPaid, 0)
                    : 0;

                return $fee;
            });

            if ($request->filled('status')) {
                $statusFilter = $request->status;
                if ($request->boolean('all')) {
                    $results = $results->where('status', $statusFilter)->values();
                } else {
                    $filtered = $results->getCollection()->where('status', $statusFilter)->values();
                    $results->setCollection($filtered);
                }
            }

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
                $a = $validated['amount'];
                unset($validated['amount']);
                $validated['base_amount'] = $a;
                $validated['payable_amount'] = $a;
                $validated['due_amount'] = $a;
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
                $amount = (float) $fee->base_amount;
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
