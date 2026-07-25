<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\SchoolClass;
use App\Http\Requests\SchoolClassRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SchoolClassController extends Controller
{
    /**
     * Helper to get school ID
     */
    private function getSchoolId($user)
    {
        return School::where('user_id', $user->id)->value('id');
    }

    public function index(Request $request)
    {
        $schoolId = $this->getSchoolId($request->user());

        if (!$schoolId) {
            return response()->json(['data' => [], 'total' => 0]);
        }

        $query = SchoolClass::where('school_id', $schoolId);

        if ($request->filled('search')) {
            $query->where('class_name', 'like', "%{$request->search}%");
        }

        return response()->json($query->orderBy('class_name', 'asc')->paginate(10));
    }

    public function store(SchoolClassRequest $request)
    {
        try {
            $schoolId = $this->getSchoolId($request->user());

            if (!$schoolId) {
                return response()->json(['message' => 'School profile not found.'], 404);
            }

            $record = DB::transaction(function () use ($schoolId, $request) {
                return SchoolClass::create([
                    'school_id' => $schoolId,
                    'class_name' => $request->validated()['class_name'],
                ]);
            });

            return response()->json(['message' => 'Record created successfully', 'data' => $record]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to create class.', 'error' => $e->getMessage()], 500);
        }
    }

    public function show(Request $request, $id)
    {
        $schoolId = $this->getSchoolId($request->user());
        return response()->json(SchoolClass::where('school_id', $schoolId)->findOrFail($id));
    }

    public function update(SchoolClassRequest $request, $id)
    {
        try {
            $schoolId = $this->getSchoolId($request->user());

            DB::transaction(function () use ($schoolId, $id, $request) {
                $record = SchoolClass::where('school_id', $schoolId)->findOrFail($id);
                $record->update(['class_name' => $request->validated()['class_name']]);
            });

            return response()->json(['message' => 'Record updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update class.', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $schoolId = $this->getSchoolId($request->user());

            DB::transaction(function () use ($schoolId, $id) {
                SchoolClass::where('school_id', $schoolId)->findOrFail($id)->delete();
            });

            return response()->json(['message' => 'Record deleted']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete class.', 'error' => $e->getMessage()], 500);
        }
    }
    
        //filter by school
    public function classFilter(Request $request)
    {
        $schoolId = $this->getSchoolId($request->user());

        if (!$schoolId) {
            return response()->json([
                'message' => 'School profile not found.'
            ], 404);
        }

        $query = SchoolClass::where('school_id', $schoolId);

        if ($request->filled('search')) {
            $query->where('class_name', 'like', '%' . $request->search . '%');
        }

        $classes = $query->latest()->paginate(10);

        return response()->json($classes);
    }
    
    
}