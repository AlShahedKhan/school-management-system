<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SchoolEmployee;
use App\Models\School; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SchoolEmployeeController extends Controller
{
    public function index(Request $request)
    {
        // 1. Find the school associated with this user
        $school = School::where('user_id', Auth::id())->first();

        if (!$school) {
            return response()->json(['data' => []], 200);
        }

        $query = SchoolEmployee::where('school_id', $school->id);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('employee_name', 'like', "%{$request->search}%")
                    ->orWhere('mobile_number', 'like', "%{$request->search}%")
                    ->orWhere('designation', 'like', "%{$request->search}%");
            });
        }

        return $query->latest()->paginate(10);
    }

    public function store(Request $request)
    {
        try {
            $data = $this->validateRequest($request);

            // 2. Map the User to their correct School ID
            $school = School::where('user_id', Auth::id())->first();

            if (!$school) {
                return response()->json([
                    'error' => 'Profile Incomplete',
                    'message' => 'We could not find a school profile linked to your user account.'
                ], 404);
            }

            // Use the actual ID from the 'schools' table
            $data['school_id'] = $school->id;

            $employee = SchoolEmployee::create($data);
            return response()->json($employee, 201);
        } catch (\Exception $e) {
            Log::error("Employee Store Error: " . $e->getMessage());
            return response()->json([
                'error' => 'Database Error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Update show, update, and destroy similarly to use $school->id
    public function show($id)
    {
        $school = School::where('user_id', Auth::id())->firstOrFail();
        return SchoolEmployee::where('school_id', $school->id)->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        try {
            $school = School::where('user_id', Auth::id())->firstOrFail();
            $employee = SchoolEmployee::where('school_id', $school->id)->findOrFail($id);

            $data = $this->validateRequest($request);
            $employee->update($data);
            return response()->json($employee);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $school = School::where('user_id', Auth::id())->firstOrFail();
        $employee = SchoolEmployee::where('school_id', $school->id)->findOrFail($id);
        $employee->delete();
        return response()->json(['message' => 'Deleted']);
    }

    protected function validateRequest(Request $request)
    {
        return $request->validate([
            'employee_name'  => 'required|string|max:255',
            'mobile_number'  => 'required|string|max:20',
            'designation'    => 'required|string|max:255',
            'monthly_leave'  => 'nullable|integer|min:0|max:31',
            'salary_amount'  => 'required|numeric|min:0',
            'payroll_date'   => 'required|date',
            'bank_name'      => 'nullable|string|max:255',
            'branch'         => 'nullable|string|max:255',
            'routing_number' => 'nullable|string|max:255',
            'ac_holder_name' => 'nullable|string|max:255',
            'ac_number'      => 'nullable|string|max:255',
        ]);
    }
}
