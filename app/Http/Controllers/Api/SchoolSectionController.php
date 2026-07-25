<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\SchoolSection;
use App\Http\Requests\SchoolSectionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SchoolSectionController extends Controller
{
    /**
     * Helper to get the school associated with the authenticated user.
     */
    private function getSchool($user)
    {
        return School::where('user_id', $user->id)->first();
    }

    public function index(Request $request)
    {
        $school = $this->getSchool($request->user());

        if (!$school) {
            return response()->json(['data' => [], 'total' => 0]);
        }

        $query = SchoolSection::with(['schoolClass', 'schoolGroup'])
            ->where('school_id', $school->id);

        // Filter by class_id
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        // Filter by group_id
        if ($request->filled('group_id')) {
            $query->where('group_id', $request->group_id);
        }

        // Filter by section name
        if ($request->filled('section_name')) {
            $query->where('section_name', 'like', "%{$request->section_name}%");
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('section_name', 'like', "%{$request->search}%")
                    ->orWhereHas('schoolClass', function ($sq) use ($request) {
                        $sq->where('class_name', 'like', "%{$request->search}%");
                    })
                    ->orWhereHas('schoolGroup', function ($sgq) use ($request) {
                        $sgq->where('group_name', 'like', "%{$request->search}%");
                    });
            });
        }

        return response()->json($query->latest()->paginate(10));
    }

    public function store(SchoolSectionRequest $request)
    {
        try {
            $school = $this->getSchool($request->user());

            if (!$school) {
                return response()->json(['message' => 'School profile not found.'], 404);
            }

            $section = DB::transaction(function () use ($school, $request) {
                return SchoolSection::create([
                    'school_id'    => $school->id,
                    'class_id'     => $request->validated()['class_id'],
                    'group_id'     => $request->validated()['group_id'] ?? null,
                    'section_name' => $request->validated()['section_name'],
                ]);
            });

            return response()->json(['message' => 'Section created successfully', 'data' => $section]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to create section.', 'error' => $e->getMessage()], 500);
        }
    }

    public function show(Request $request, $id)
    {
        $school = $this->getSchool($request->user());
        $section = SchoolSection::where('school_id', $school->id)->findOrFail($id);

        return response()->json($section);
    }

    public function update(SchoolSectionRequest $request, $id)
    {
        try {
            $school = $this->getSchool($request->user());

            DB::transaction(function () use ($school, $id, $request) {
                $section = SchoolSection::where('school_id', $school->id)->findOrFail($id);
                $section->update([
                    'class_id'     => $request->validated()['class_id'],
                    'group_id'     => $request->validated()['group_id'] ?? null,
                    'section_name' => $request->validated()['section_name'],
                ]);
            });

            return response()->json(['message' => 'Section updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update section.', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $school = $this->getSchool($request->user());

            DB::transaction(function () use ($school, $id) {
                SchoolSection::where('school_id', $school->id)->findOrFail($id)->delete();
            });

            return response()->json(['message' => 'Section deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete section.', 'error' => $e->getMessage()], 500);
        }
    }
    
        //School section filter
    public function sectionFilter(Request $request)
    {
        $school = $this->getSchool($request->user());

        if (!$school) {
            return response()->json([
                'message' => 'School profile not found.'
            ], 404);
        }

        $query = SchoolSection::with(['schoolClass', 'schoolGroup'])
            ->where('school_id', $school->id);

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('section_name', 'like', "%{$search}%")
                ->orWhereHas('schoolClass', function ($sq) use ($search) {
                    $sq->where('class_name', 'like', "%{$search}%");
                })
                ->orWhereHas('schoolGroup', function ($sg) use ($search) {
                    $sg->where('group_name', 'like', "%{$search}%");
                });
            });
        }

        $sections = $query->latest()->paginate(10);

        return response()->json($sections);
    }
}