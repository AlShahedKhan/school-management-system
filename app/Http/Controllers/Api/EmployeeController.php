<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $school = School::where('user_id', Auth::id())->first();
        if (!$school) {
            return response()->json(['data' => []], 200);
        }

        $query = Employee::where('school_id', $school->id);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('mobile_number', 'like', "%{$search}%")
                  ->orWhere('designation', 'like', "%{$search}%")
                  ->orWhere('employee_no', 'like', "%{$search}%");
            });
        }

        if ($request->filled('employee_id')) {
            $query->where('id', $request->employee_id);
        }

        if ($request->filled('month')) {
            $query->whereMonth('salary_start_date', $request->month);
        }

        if ($request->filled('year')) {
            $query->whereYear('salary_start_date', $request->year);
        }

        if ($request->filled('designation')) {
            $query->where('designation', 'like', "%" . trim($request->designation) . "%");
        }

        $employees = $query->orderBy('id', 'desc')->paginate(30);

        return response()->json($employees);
    }

    public function store(Request $request)
    {
        $school = School::where('user_id', Auth::id())->firstOrFail();

        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'designation'       => 'required|string|max:255',
            'mobile_number'     => 'required|string|max:20',
            'salary_amount'     => 'required|numeric|min:0',
            'salary_start_date' => 'required|date',
            'pay_date'          => 'required|string|max:50',
            'employee_status'   => 'required|string',
        ]);

        $validated['school_id'] = $school->id;
        $maxId = Employee::max('id') ?? 0;
        $validated['employee_no'] = 'EMP-' . str_pad((string) ($maxId + 1), 5, '0', STR_PAD_LEFT);

        $employee = Employee::create($validated);

        return response()->json([
            'message' => 'Employee registered successfully',
            'data'    => $employee
        ], 201);
    }

    public function show($id)
    {
        $school = School::where('user_id', Auth::id())->firstOrFail();
        $employee = Employee::where('school_id', $school->id)->findOrFail($id);

        return response()->json([
            'data' => $employee
        ]);
    }

    public function update(Request $request, $id)
    {
        $school = School::where('user_id', Auth::id())->firstOrFail();
        $employee = Employee::where('school_id', $school->id)->findOrFail($id);

        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'designation'       => 'required|string|max:255',
            'mobile_number'     => 'required|string|max:20',
            'salary_amount'     => 'required|numeric|min:0',
            'salary_start_date' => 'required|date',
            'pay_date'          => 'required|string|max:50',
            'employee_status'   => 'required|string',
        ]);

        $employee->update($validated);

        return response()->json([
            'message' => 'Employee updated successfully',
            'data'    => $employee
        ]);
    }

    public function destroy($id)
    {
        $school = School::where('user_id', Auth::id())->firstOrFail();
        $employee = Employee::where('school_id', $school->id)->findOrFail($id);
        $employee->delete();

        return response()->json([
            'message' => 'Employee deleted successfully'
        ]);
    }
}
