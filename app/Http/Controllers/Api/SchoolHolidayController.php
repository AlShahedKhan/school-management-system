<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SchoolHoliday;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class SchoolHolidayController extends Controller
{
    public function index(Request $request)
    {
        $school = School::where('user_id', Auth::id())->first();

        if (!$school) {
            return response()->json(['message' => 'School not found for this user.'], 404);
        }

        $query = SchoolHoliday::where('school_id', $school->id);

        // Search filter
        if ($request->search) {
            $query->where('reason', 'like', '%' . $request->search . '%');
        }

        // Modal Filters
        if ($request->class_name) {
            $query->where('class_name', $request->class_name);
        }
        if ($request->group_name) {
            $query->where('group_name', $request->group_name);
        }
        if ($request->section_name) {
            $query->where('section_name', $request->section_name);
        }
        if ($request->session) {
            $query->where('session', $request->session);
        }

        return $query->orderBy('start_date', 'desc')->paginate(10);
    }

    public function store(Request $request)
    {
        // 1. Find the school associated with this user
        $school = School::where('user_id', Auth::id())->first();

        if (!$school) {
            return response()->json(['message' => 'Unauthorized: No school linked to your account.'], 403);
        }

        try {
            // 2. Validate the request
            $validatedData = $request->validate([
                'type'         => 'required|string',
                'reason'       => 'required|string|max:255',
                'start_date'   => 'required|date',
                'end_date'     => 'required|date|after_or_equal:start_date',
                'total_days'   => 'required|integer|min:1',
                'class_name'   => 'nullable|string',
                'section_name' => 'nullable|string',
                'group_name'   => 'nullable|string',
                'session'      => 'nullable|string',
            ]);

            // 3. Add school_id to the data
            $validatedData['school_id'] = $school->id;

            // 4. Create record
            $holiday = SchoolHoliday::create($validatedData);

            return response()->json([
                'status'  => 'success',
                'message' => 'Holiday recorded successfully',
                'data'    => $holiday
            ], 201);
        } catch (Exception $e) {
            // Log the error for the developer
            Log::error("Holiday Store Error: " . $e->getMessage());

            // Return full details error for the SweetAlert
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to save holiday record.',
                'error'   => $e->getMessage(), // Technical detail
                'line'    => $e->getLine()     // Location of error
            ], 500);
        }
    }

    public function show($id)
    {
        $school = School::where('user_id', Auth::id())->first();
        return SchoolHoliday::where('school_id', $school->id)->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $school = School::where('user_id', Auth::id())->first();
        $holiday = SchoolHoliday::where('school_id', $school->id)->findOrFail($id);

        try {
            $validatedData = $request->validate([
                'type'         => 'required|string',
                'reason'       => 'required|string|max:255',
                'start_date'   => 'required|date',
                'end_date'     => 'required|date|after_or_equal:start_date',
                'total_days'   => 'required|integer|min:1',
                'class_name'   => 'nullable|string',
                'section_name' => 'nullable|string',
                'group_name'   => 'nullable|string',
                'session'      => 'nullable|string',
            ]);

            $holiday->update($validatedData);

            return response()->json([
                'status'  => 'success',
                'message' => 'Holiday updated successfully',
                'data'    => $holiday
            ]);
        } catch (Exception $e) {
            Log::error("Holiday Update Error: " . $e->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to update holiday record.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $school = School::where('user_id', Auth::id())->first();
            $holiday = SchoolHoliday::where('school_id', $school->id)->findOrFail($id);
            $holiday->delete();

            return response()->json(['message' => 'Holiday removed successfully']);
        } catch (Exception $e) {
            return response()->json(['message' => 'Delete failed', 'error' => $e->getMessage()], 500);
        }
    }
}
