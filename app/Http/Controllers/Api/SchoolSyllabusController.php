<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SchoolSyllabusRequest;
use App\Models\School;
use App\Models\SchoolSyllabus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SchoolSyllabusController extends Controller
{
    private function getSchool()
    {
        return School::where('user_id', Auth::id())->first();
    }

    public function index(Request $request)
    {
        $school = $this->getSchool();
        if (!$school) return response()->json(['data' => [], 'total' => 0]);

        $query = SchoolSyllabus::where('school_id', $school->id)->with([
            'school_session',
            'school_class',
            'school_group',
            'school_section',
            'school_subject',
            'school_exam'
        ]);

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('group_id')) {
            $query->where('group_id', $request->group_id);
        }

        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        if ($request->filled('session_id')) {
            $query->where('session_id', $request->session_id);
        }

        if ($request->filled('exam_id')) {
            $query->where('exam_id', $request->exam_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('start_page', 'like', "%{$search}%")
                    ->orWhere('end_page', 'like', "%{$search}%")
                    ->orWhereHas('school_exam', function ($ex) use ($search) {
                        $ex->where('exam_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('school_subject', function ($sub) use ($search) {
                        $sub->where('subject_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('school_session', function ($sess) use ($search) {
                        $sess->where('session_year', 'like', "%{$search}%");
                    })
                    ->orWhereHas('school_class', function ($cls) use ($search) {
                        $cls->where('class_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('school_section', function ($sec) use ($search) {
                        $sec->where('section_name', 'like', "%{$search}%");
                    });
            });
        }

        return response()->json($query->latest()->paginate(10));
    }

    public function store(SchoolSyllabusRequest $request)
    {
        try {
            $school = $this->getSchool();
            if (!$school) return response()->json(['message' => 'School profile not found.'], 404);

            $syllabus = DB::transaction(function () use ($school, $request) {
                return SchoolSyllabus::create(array_merge($request->validated(), [
                    'school_id' => $school->id,
                ]));
            });

            return response()->json(['message' => 'Syllabus created successfully', 'data' => $syllabus]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to create syllabus.', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $school = $this->getSchool();
        if (!$school) return response()->json(['message' => 'School profile not found.'], 404);

        $syllabus = SchoolSyllabus::where('school_id', $school->id)->with([
            'school_session',
            'school_class',
            'school_group',
            'school_section',
            'school_subject',
            'school_exam'
        ])->find($id);

        if (!$syllabus) {
            return response()->json(['message' => 'Syllabus not found'], 404);
        }

        return response()->json($syllabus);
    }

    public function update(SchoolSyllabusRequest $request, $id)
    {
        try {
            $school = $this->getSchool();
            if (!$school) return response()->json(['message' => 'School profile not found.'], 404);

            DB::transaction(function () use ($school, $id, $request) {
                $syllabus = SchoolSyllabus::where('school_id', $school->id)->findOrFail($id);
                $syllabus->update($request->validated());
            });

            return response()->json(['message' => 'Syllabus updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update syllabus.', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $school = $this->getSchool();
            if (!$school) return response()->json(['message' => 'School profile not found.'], 404);

            DB::transaction(function () use ($school, $id) {
                SchoolSyllabus::where('school_id', $school->id)->findOrFail($id)->delete();
            });

            return response()->json(['message' => 'Deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete syllabus.', 'error' => $e->getMessage()], 500);
        }
    }

    public function syllabusFilter(Request $request)
    {
        $school = $this->getSchool();

        if (!$school) {
            return response()->json([
                'message' => 'School profile not found.',
                'data' => []
            ], 404);
        }

        $query = SchoolSyllabus::where('school_id', $school->id)
            ->with([
                'school_session',
                'school_class',
                'school_group',
                'school_section',
                'school_subject',
                'school_exam'
            ]);

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('start_page', 'like', "%{$search}%")
                ->orWhere('end_page', 'like', "%{$search}%")
                ->orWhereHas('school_exam', function ($ex) use ($search) {
                    $ex->where('exam_name', 'like', "%{$search}%");
                })
                ->orWhereHas('school_subject', function ($sub) use ($search) {
                    $sub->where('subject_name', 'like', "%{$search}%");
                })
                ->orWhereHas('school_session', function ($sess) use ($search) {
                    $sess->where('session_year', 'like', "%{$search}%");
                })
                ->orWhereHas('school_class', function ($cls) use ($search) {
                    $cls->where('class_name', 'like', "%{$search}%");
                })
                ->orWhereHas('school_group', function ($grp) use ($search) {
                    $grp->where('group_name', 'like', "%{$search}%");
                })
                ->orWhereHas('school_section', function ($sec) use ($search) {
                    $sec->where('section_name', 'like', "%{$search}%");
                });
            });
        }

        $syllabuses = $query->latest()->paginate(10);

        if ($syllabuses->total() == 0) {
            return response()->json([
                'message' => 'No syllabus found for "' . $request->search . '"',
                'data' => [],
                'total' => 0
            ]);
        }

        return response()->json($syllabuses);
    }
}
