<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdvancePayment;
use App\Models\School;
use App\Models\SchoolStudentFee;
use App\Models\SchoolFeeTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdvancePaymentController extends Controller
{
    private function getSchool()
    {
        $school = School::where('user_id', Auth::id())->first();
        if (!$school) {
            abort(403, 'Unauthorized: No school associated with this account.');
        }
        return $school;
    }

    public function checkDue(Request $request)
    {
        $school = $this->getSchool();

        $validated = $request->validate([
            'student_id' => 'required|exists:admission_students,id',
        ]);

        $dueFees = SchoolStudentFee::where('school_id', $school->id)
            ->where('student_id', $validated['student_id'])
            ->whereIn('status', ['pending', 'partial_paid', 'due', 'due_partial', 'over_due', 'over_due_partial'])
            ->get();

        $totalDue = $dueFees->sum(function ($fee) {
            $paid = DB::table('school_payments')
                ->where('admission_student_id', $fee->student_id)
                ->where('fees_type', $fee->fee_type_name)
                ->where('fee_name', $fee->fee_name)
                ->sum('type_amount');
            return max((float) $fee->amount - (float) $paid, 0);
        });

        return response()->json([
            'has_due'  => $totalDue > 0,
            'due_amount' => round($totalDue, 2),
        ]);
    }

    public function getMonthlyFee(Request $request)
    {
        $school = $this->getSchool();

        $validated = $request->validate([
            'student_id' => 'required|exists:admission_students,id',
        ]);

        $student = DB::table('admission_students')->find($validated['student_id']);
        if (!$student) {
            return response()->json(['monthly_fee' => 0]);
        }

        $template = SchoolFeeTemplate::where('school_id', $school->id)
            ->where('frequency', 'monthly')
            ->where('is_active', 1)
            ->where(function ($q) use ($student) {
                $q->where('class_id', $student->class)
                  ->orWhereHas('schoolClass', function ($sq) use ($student) {
                      $sq->where('class_name', $student->class);
                  });
            })
            ->first();

        return response()->json([
            'monthly_fee' => $template ? (float) $template->amount : 0,
            'fee_name'    => $template ? $template->fee_name : null,
            'fee_type'    => $template ? $template->fee_type_name : null,
        ]);
    }

    public function store(Request $request)
    {
        $school = $this->getSchool();

        $validated = $request->validate([
            'student_id'  => 'required|exists:admission_students,id',
            'amount'      => 'required|numeric|min:1',
            'monthly_fee' => 'required|numeric|min:0',
            'pay_date'    => 'required|date',
            'pay_method'  => 'required|string',
            'notes'       => 'nullable|string|max:500',
        ]);

        $amount = (float) $validated['amount'];
        $monthlyFee = (float) $validated['monthly_fee'];

        if ($monthlyFee > 0) {
            $fullMonths = (int) ($amount / $monthlyFee);
            $partialCredit = $amount - ($fullMonths * $monthlyFee);
        } else {
            $fullMonths = 0;
            $partialCredit = $amount;
        }

        $advance = AdvancePayment::create([
            'school_id'        => $school->id,
            'student_id'       => $validated['student_id'],
            'amount'           => $amount,
            'monthly_fee'      => $monthlyFee,
            'full_months'      => $fullMonths,
            'partial_credit'   => $partialCredit,
            'remaining_credit' => $amount,
            'pay_date'         => $validated['pay_date'],
            'pay_method'       => $validated['pay_method'],
            'notes'            => $validated['notes'] ?? null,
        ]);

        return response()->json([
            'status' => 'success',
            'data'   => $advance->load('student'),
            'message' => 'Advance payment recorded successfully.',
        ], 201);
    }

    public function index(Request $request)
    {
        $school = $this->getSchool();

        $advances = AdvancePayment::with('student')
            ->where('school_id', $school->id)
            ->latest()
            ->paginate($request->input('per_page', 30));

        return response()->json($advances);
    }
}
