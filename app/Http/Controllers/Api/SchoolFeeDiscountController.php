<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SchoolFeeDiscount;
use App\Models\SchoolPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SchoolFeeDiscountController extends Controller
{
    private function getSchool($user)
    {
        return DB::table('schools')->where('user_id', $user->id)->first();
    }

    /**
     * After saving a discount, update any existing payment records for that
     * student + fee so total_payable reflects the discounted amount and
     * payable_due / status are recalculated accordingly.
     */
    private function applyDiscountToPayments(int $schoolId, int $studentId, string $feeTypeName, string $feeName, float $afterDiscount): void
    {
        // Only touch records that are not fully paid
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

        return $request->boolean('all')
            ? response()->json(['data' => $query->latest()->get()])
            : response()->json($query->latest()->paginate(10));
    }

    public function store(Request $request)
    {
        $school = $this->getSchool($request->user());

        if (!$school) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'student_id'      => [
                'required',
                'exists:admission_students,id',
                Rule::unique('school_fee_discounts')->where(function ($query) use ($request, $school) {
                    return $query->where('fee_type_id', $request->fee_type_id)
                        ->where('session_id', $request->session_id)
                        ->where('school_id', $school->id)
                        ->where('student_id', $request->student_id);
                })
            ],
            'class_id'        => 'required|exists:school_classes,id',
            'session_id'      => 'required|exists:school_sessions,id',
            'fee_type_id'     => 'required|exists:school_fee_templates,id',
            'fee_name'        => 'required|string|max:255', // Added validation for fee_name
            'discount_type'   => 'required|in:Fixed,Percentage',
            'discount_value'  => 'required|numeric|min:0',
            'before_discount' => 'required|numeric',
            'discount_amount' => 'required|numeric',
            'after_discount'  => 'required|numeric',
            'group_id'        => 'nullable',
            'section_id'      => 'nullable',
            'start_date'      => 'nullable|date',
            'end_date'        => 'nullable|date|after_or_equal:start_date',
        ], [
            'student_id.unique' => 'This student already has a discount for this fee type in this session.',
            'end_date.after_or_equal' => 'The end date must be a date after or equal to the start date.'
        ]);

        $feeDiscount = SchoolFeeDiscount::create(array_merge($validated, [
            'school_id' => $school->id
        ]));

        // Resolve the fee_type_name from the fee type record so we can match payments
        $feeType = DB::table('school_fee_templates')->where('id', $validated['fee_type_id'])->first();
        if ($feeType) {
            $this->applyDiscountToPayments(
                $school->id,
                $validated['student_id'],
                $feeType->fee_type_name,
                $validated['fee_name'],
                (float) $validated['after_discount']
            );
        }

        return response()->json(['status' => 'success', 'data' => $feeDiscount]);
    }

    public function show($id)
    {
        $discount = SchoolFeeDiscount::with([
            'student',
            'feeType',
            'schoolClass',
            'schoolSession',
            'schoolGroup',
            'schoolSection'
        ])->findOrFail($id);

        return response()->json($discount);
    }

    public function update(Request $request, $id)
    {
        $school = $this->getSchool($request->user());
        $discount = SchoolFeeDiscount::where('school_id', $school->id)->findOrFail($id);

        $validated = $request->validate([
            'student_id' => [
                'required',
                'exists:admission_students,id',
                Rule::unique('school_fee_discounts')->where(function ($query) use ($request, $school) {
                    return $query->where('fee_type_id', $request->fee_type_id)
                        ->where('session_id', $request->session_id)
                        ->where('school_id', $school->id)
                        ->where('student_id', $request->student_id);
                })->ignore($id)
            ],
            'class_id'        => 'required|exists:school_classes,id',
            'session_id'      => 'required|exists:school_sessions,id',
            'fee_type_id'     => 'required|exists:school_fee_templates,id',
            'fee_name'        => 'required|string|max:255', // Added validation for fee_name
            'discount_type'   => 'required|in:Fixed,Percentage',
            'discount_value'  => 'required|numeric|min:0',
            'before_discount' => 'required|numeric',
            'discount_amount' => 'required|numeric',
            'after_discount'  => 'required|numeric',
            'group_id'        => 'nullable',
            'section_id'      => 'nullable',
            'start_date'      => 'nullable|date',
            'end_date'        => 'nullable|date|after_or_equal:start_date',
        ]);

        $discount->update($validated);

        // Re-apply the updated discount to any existing payment records
        $feeType = DB::table('school_fee_templates')->where('id', $validated['fee_type_id'])->first();
        if ($feeType) {
            $this->applyDiscountToPayments(
                $school->id,
                $validated['student_id'],
                $feeType->fee_type_name,
                $validated['fee_name'],
                (float) $validated['after_discount']
            );
        }

        return response()->json(['status' => 'success', 'data' => $discount]);
    }

    public function destroy(Request $request, $id)
    {
        $school = $this->getSchool($request->user());
        $discount = SchoolFeeDiscount::where('school_id', $school->id)->findOrFail($id);
        $discount->delete();
        return response()->json(['status' => 'success']);
    }
}
