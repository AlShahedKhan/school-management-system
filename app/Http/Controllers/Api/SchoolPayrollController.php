<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SchoolPayroll;
use App\Models\SchoolEmployee;
use App\Models\School; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        $payroll = SchoolPayroll::create($data);
        
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
        $payroll->delete();
        
        return response()->json(['message' => 'Deleted']);
    }
}