<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SchoolPayroll;
use App\Models\SchoolEmployee;
use App\Models\School;
use App\Services\AccountService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SchoolPayrollController extends Controller
{
    public function index(Request $request)
    {
        // 1. Find the school associated with this user
        $school = School::where('user_id', Auth::id())->first();

        if (!$school) {
            return response()->json(['message' => 'School record not found.'], 404);
        }

        $query = SchoolPayroll::where('school_id', $school->id);

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('employee_name', 'like', "%{$request->search}%")
                  ->orWhere('month', 'like', "%{$request->search}%");
            });
        }

        return $query->latest()->paginate(10);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'school_employee_id' => 'required|exists:school_employees,id',
            'month'              => 'required|string',
            'year'               => 'required|integer',
            'present'            => 'required|integer',
            'absent'             => 'required|integer',
            'leave'              => 'required|integer',
            'total_payable'      => 'required|numeric',
            'payable_due'        => 'required|numeric',
            'advance_status'     => 'required|in:Yes,No',
            'pay_type'           => 'required|string',
            'paid_amount'        => 'required|numeric',
        ]);

        // 1. Find the school associated with this user
        $school = School::where('user_id', Auth::id())->first();

        if (!$school) {
            return response()->json(['message' => 'Unauthorized: No school associated with this user.'], 403);
        }

        $employee = SchoolEmployee::findOrFail($request->school_employee_id);

        $data['school_id'] = $school->id;
        $data['employee_name'] = $employee->employee_name;
        $data['mobile_number'] = $employee->mobile_number;
        $data['designation'] = $employee->designation;

        $payroll = DB::transaction(function () use ($data, $school) {
            $payroll = SchoolPayroll::create($data);

            $paidAmount = (float) $data['paid_amount'];
            if ($paidAmount > 0) {
                // Cash Out from the internal System Cash Balance (immutable ledger)
                app(AccountService::class)->cashOut(
                    $school->id,
                    $paidAmount,
                    'Payroll',
                    $payroll->id,
                    [
                        'remarks' => 'Payroll - ' . ($data['employee_name'] ?? '') . ' | ' . ($data['month'] ?? '') . ' ' . ($data['year'] ?? ''),
                    ]
                );
            }

            return $payroll;
        });
        
        return response()->json($payroll, 201);
    }

    public function destroy($id)
    {
        // 1. Find the school associated with this user
        $school = School::where('user_id', Auth::id())->first();

        if (!$school) {
            return response()->json(['message' => 'School record not found.'], 404);
        }

        $payroll = SchoolPayroll::where('school_id', $school->id)->findOrFail($id);

        DB::transaction(function () use ($school, $payroll) {
            $paidAmount = (float) $payroll->paid_amount;
            $payrollId = $payroll->id;
            $payroll->delete();

            if ($paidAmount > 0) {
                app(AccountService::class)->cashIn($school->id, $paidAmount, 'Payroll Adjustment', $payrollId, [
                    'remarks' => 'Reversal of deleted payroll #' . $payrollId,
                ]);
            }
        });

        return response()->json(['message' => 'Deleted']);
    }
}