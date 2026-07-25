<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SchoolAnnouncement;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use Exception;

class SchoolAnnouncementController extends Controller
{
    /**
     * Helper to get school_id via the School model relationship.
     */
    private function getSchoolId()
    {
        $school = School::where('user_id', Auth::id())->first();
        return $school ? $school->id : null;
    }

    /**
     * Display a listing of the announcements.
     */
    public function index(Request $request)
    {
        $school_id = $this->getSchoolId();

        if (!$school_id) {
            return response()->json(['message' => 'School profile not found.'], 404);
        }

        $query = SchoolAnnouncement::where('school_id', $school_id);

        // 1. Global Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('details', 'like', '%' . $search . '%');
            });
        }

        // 2. Specific Dropdown Filters (Modal)
        $query->when($request->filled('class_name'), function ($q) use ($request) {
            return $q->where('class_name', $request->class_name);
        });

        $query->when($request->filled('group_name'), function ($q) use ($request) {
            return $q->where('group_name', $request->group_name);
        });

        $query->when($request->filled('section_name'), function ($q) use ($request) {
            return $q->where('section_name', $request->section_name);
        });

        $query->when($request->filled('session'), function ($q) use ($request) {
            return $q->where('session', $request->session);
        });

        return $query->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10);
    }

    /**
     * Store a newly created announcement.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type'         => 'required|in:General,Class Wise',
            'title'        => 'required|string|max:255',
            'details'      => 'required|string',
            'date'         => 'required|date',
            'class_name'   => 'required_if:type,Class Wise|nullable',
            'section_name' => 'required_if:type,Class Wise|nullable',
            'session'      => 'required_if:type,Class Wise|nullable',
        ], [
            'class_name.required_if' => 'Please select a Class for Class Specific notices.',
            'section_name.required_if' => 'Please select a Section.',
            'session.required_if' => 'Session year is required.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            // 1. Find the school associated with this user
            $school = School::where('user_id', Auth::id())->first();

            if (!$school) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: No school associated with this user account.'
                ], 403);
            }

            $data = $request->only([
                'type',
                'title',
                'details',
                'date',
                'class_name',
                'group_name',
                'section_name',
                'session'
            ]);

            // Assign the found school id
            $data['school_id'] = $school->id;

            // Data Sanitization
            if ($data['type'] === 'General') {
                $data['class_name'] = $data['group_name'] = $data['section_name'] = $data['session'] = null;
            }

            $announcement = SchoolAnnouncement::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Announcement created successfully',
                'data'    => $announcement
            ], 201);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Database Error: Save failed.',
                'error_detail' => $e->getMessage()
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'System Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified announcement.
     */
    public function show($id)
    {
        $school_id = $this->getSchoolId();
        return SchoolAnnouncement::where('school_id', $school_id)->findOrFail($id);
    }

    /**
     * Update the specified announcement.
     */
    public function update(Request $request, $id)
    {
        try {
            $school_id = $this->getSchoolId();
            $announcement = SchoolAnnouncement::where('school_id', $school_id)->findOrFail($id);

            $validator = Validator::make($request->all(), [
                'type'         => 'required|in:General,Class Wise',
                'title'        => 'required|string|max:255',
                'details'      => 'required|string',
                'date'         => 'required|date',
                'class_name'   => 'required_if:type,Class Wise|nullable',
                'section_name' => 'required_if:type,Class Wise|nullable',
                'session'      => 'required_if:type,Class Wise|nullable',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation Error',
                    'errors'  => $validator->errors()
                ], 422);
            }

            $data = $request->all();

            if ($request->type === 'General') {
                $data['class_name'] = $data['group_name'] = $data['section_name'] = $data['session'] = null;
            }

            $announcement->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Announcement updated successfully',
                'data'    => $announcement
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Update failed due to database error.',
                'error_detail' => $e->getMessage()
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified announcement.
     */
    public function destroy($id)
    {
        try {
            $school_id = $this->getSchoolId();
            $announcement = SchoolAnnouncement::where('school_id', $school_id)->findOrFail($id);
            $announcement->delete();

            return response()->json(['success' => true, 'message' => 'Deleted successfully']);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Delete failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
