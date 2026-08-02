<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeePayroll;
use App\Models\School;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EmployeePayrollController extends Controller
{
    public function index(Request $request)
    {
        $school = School::where('user_id', Auth::id())->first();
        if (!$school) {
            return response()->json(['data' => []], 200);
        }

        $query = EmployeePayroll::where('school_id', $school->id)->with(['employee', 'teacher']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('month')) {
            $query->where('receive_month', $request->month);
        }

        if ($request->filled('year')) {
            $query->where('receive_year', $request->year);
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->whereHas('employee', function ($eq) use ($search) {
                    $eq->where('name', 'like', "%{$search}%")
                       ->orWhere('mobile_number', 'like', "%{$search}%")
                       ->orWhere('designation', 'like', "%{$search}%");
                })->orWhereHas('teacher', function ($tq) use ($search) {
                    $tq->where('name', 'like', "%{$search}%")
                       ->orWhere('mobile', 'like', "%{$search}%")
                       ->orWhere('designation', 'like', "%{$search}%");
                });
            });
        }

        $payrolls = $query->orderBy('id', 'desc')->paginate(30);

        return response()->json($payrolls);
    }

    public function staffDetails(Request $request)
    {
        $school = School::where('user_id', Auth::id())->firstOrFail();
        $type = $request->query('type', 'employee');
        $id = $request->query('id');

        if (!$id) {
            return response()->json(['message' => 'Staff ID is required'], 400);
        }

        if ($type === 'teacher') {
            $staff = Teacher::where('school_id', $school->id)->findOrFail($id);
            $salary = (float) ($staff->salary_amount ?? 0);
            $totalPaid = (float) EmployeePayroll::where('school_id', $school->id)
                ->where('teacher_id', $id)
                ->sum('receive_amount');

            $duesData = $this->calculateStaffDues($staff, $totalPaid);

            return response()->json([
                'id'          => $staff->id,
                'type'        => 'teacher',
                'name'        => $staff->name,
                'designation' => $staff->designation ?? 'Teacher',
                'mobile'      => $staff->mobile ?? '-',
                'salary'      => $salary,
                'due'         => $duesData['due'],
                'overdue'     => $duesData['overdue'],
                'total_due'   => $duesData['total_due'],
            ]);
        }

        $staff = Employee::where('school_id', $school->id)->findOrFail($id);
        $salary = (float) ($staff->salary_amount ?? 0);
        $totalPaid = (float) EmployeePayroll::where('school_id', $school->id)
            ->where('employee_id', $id)
            ->sum('receive_amount');

        $duesData = $this->calculateStaffDues($staff, $totalPaid);

        return response()->json([
            'id'          => $staff->id,
            'type'        => 'employee',
            'name'        => $staff->name,
            'designation' => $staff->designation ?? 'Employee',
            'mobile'      => $staff->mobile_number ?? '-',
            'salary'      => $salary,
            'due'         => $duesData['due'],
            'overdue'     => $duesData['overdue'],
            'total_due'   => $duesData['total_due'],
        ]);
    }

    private function calculateStaffDues($staff, $totalPaid)
    {
        $startDateStr = $staff->salary_start_date;
        $monthlySalary = (float) ($staff->salary_amount ?? 0);
        
        if (!$startDateStr || $monthlySalary <= 0) {
            return ['due' => 0.0, 'overdue' => 0.0, 'total_due' => 0.0];
        }

        $startDate = Carbon::parse($startDateStr)->startOfMonth();
        $currentDate = Carbon::now()->startOfMonth();

        if ($startDate->isAfter($currentDate)) {
            return ['due' => 0.0, 'overdue' => 0.0, 'total_due' => 0.0];
        }

        $monthsDifference = $startDate->diffInMonths($currentDate) + 1;
        $totalPayable = $monthsDifference * $monthlySalary;
        $previousPayable = ($monthsDifference > 1) ? ($monthsDifference - 1) * $monthlySalary : 0.0;

        $totalDue = max(0.0, $totalPayable - $totalPaid);
        $overdue = max(0.0, $previousPayable - $totalPaid);
        $due = max(0.0, $totalDue - $overdue);

        return [
            'due' => $due,
            'overdue' => $overdue,
            'total_due' => $totalDue
        ];
    }

    public function store(Request $request)
    {
        $school = School::where('user_id', Auth::id())->firstOrFail();

        $type = $request->input('type', 'employee');

        $rules = [
            'type'           => 'required|in:employee,teacher',
            'pay_type'       => 'nullable|string',
            'receive_amount' => 'required|numeric|min:1',
            'receive_month'  => 'required|integer|between:1,12',
            'receive_year'   => 'required|integer|min:2020',
            'receive_date'   => 'required|date',
            'payment_method' => 'nullable|string',
            'note'           => 'nullable|string',
        ];

        if ($type === 'teacher') {
            $rules['teacher_id'] = 'required|exists:teachers,id';
        } else {
            $rules['employee_id'] = 'required|exists:employees,id';
        }

        $validated = $request->validate($rules);
        $validated['school_id'] = $school->id;
        $validated['type'] = $type;

        // Calculate Salary Status
        $monthlySalary = 0;
        if ($type === 'teacher') {
            $teacher = Teacher::where('school_id', $school->id)->find($validated['teacher_id']);
            $monthlySalary = (float) ($teacher->salary_amount ?? 0);
        } else {
            $employee = Employee::where('school_id', $school->id)->find($validated['employee_id']);
            $monthlySalary = (float) ($employee->salary_amount ?? 0);
        }

        $paidThisMonth = (float) EmployeePayroll::where('school_id', $school->id)
            ->when($type === 'teacher', fn($q) => $q->where('teacher_id', $validated['teacher_id']))
            ->when($type === 'employee', fn($q) => $q->where('employee_id', $validated['employee_id']))
            ->where('receive_month', $validated['receive_month'])
            ->where('receive_year', $validated['receive_year'])
            ->sum('receive_amount');

        $newTotal = $paidThisMonth + (float) $validated['receive_amount'];

        if ($monthlySalary > 0 && $newTotal >= $monthlySalary) {
            $validated['salary_type'] = 'paid';
        } else {
            $validated['salary_type'] = 'partial_paid';
        }

        $payroll = EmployeePayroll::create($validated);

        return response()->json([
            'message' => 'Payroll saved successfully',
            'data'    => $payroll->load(['employee', 'teacher']),
        ], 201);
    }

    public function show($id)
    {
        $school = School::where('user_id', Auth::id())->firstOrFail();
        $payroll = EmployeePayroll::where('school_id', $school->id)->with(['employee', 'teacher'])->findOrFail($id);

        return response()->json(['data' => $payroll]);
    }

    public function destroy($id)
    {
        $school = School::where('user_id', Auth::id())->firstOrFail();
        $payroll = EmployeePayroll::where('school_id', $school->id)->findOrFail($id);
        $payroll->delete();

        return response()->json(['message' => 'Payroll record deleted successfully']);
    }
}
