<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SchoolSubjectRequest;
use App\Models\School;
use App\Models\SchoolSubject;
use App\Models\SchoolExamGrade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SchoolSubjectController extends Controller
{
    private function getSchool()
    {
        return School::where('user_id', Auth::id())->first();
    }

    public function index(Request $request)
    {
        $school = $this->getSchool();
        if (!$school) return response()->json(['data' => [], 'total' => 0]);

        $query = SchoolSubject::where('school_id', $school->id)
            ->with(['school_class', 'school_group', 'school_section', 'grade_type']);

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('group_id')) {
            $query->where('group_id', $request->group_id);
        }

        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('subject_name', 'like', "%{$request->search}%")
                    ->orWhereHas('school_class', function ($subQ) use ($request) {
                        $subQ->where('class_name', 'like', "%{$request->search}%");
                    });
            });
        }

        return response()->json($query->latest()->paginate(10));
    }

    public function store(SchoolSubjectRequest $request)
    {
        try {
            $school = $this->getSchool();
            if (!$school) return response()->json(['message' => 'School profile not found.'], 404);

            $subject = DB::transaction(function () use ($school, $request) {
                return SchoolSubject::create(array_merge($request->validated(), [
                    'school_id' => $school->id,
                ]));
            });

            return response()->json(['message' => 'Subject created successfully', 'data' => $subject]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to create subject.', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $school = $this->getSchool();
        if (!$school) return response()->json(['message' => 'School profile not found.'], 404);

        return SchoolSubject::where('school_id', $school->id)->with(['school_class', 'school_group', 'school_section', 'grade_type'])->findOrFail($id);
    }

    public function update(SchoolSubjectRequest $request, $id)
    {
        try {
            $school = $this->getSchool();
            if (!$school) return response()->json(['message' => 'School profile not found.'], 404);

            DB::transaction(function () use ($school, $id, $request) {
                $subject = SchoolSubject::where('school_id', $school->id)->findOrFail($id);
                $subject->update($request->validated());
            });

            return response()->json(['message' => 'Subject updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update subject.', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $school = $this->getSchool();
            if (!$school) return response()->json(['message' => 'School profile not found.'], 404);

            DB::transaction(function () use ($school, $id) {
                SchoolSubject::where('school_id', $school->id)->findOrFail($id)->delete();
            });

            return response()->json(['message' => 'Deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete subject.', 'error' => $e->getMessage()], 500);
        }
    }

    public function subjectFilter(Request $request)
    {
        $school = $this->getSchool();

        if (!$school) {
            return response()->json([
                'message' => 'School profile not found.',
                'data' => []
            ], 404);
        }

        $query = SchoolSubject::where('school_id', $school->id)
            ->with([
                'school_class',
                'school_group',
                'school_section',
                'grade_type'
            ]);

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('subject_name', 'like', "%{$search}%")
                ->orWhereHas('school_class', function ($sq) use ($search) {
                    $sq->where('class_name', 'like', "%{$search}%");
                })
                ->orWhereHas('school_group', function ($gq) use ($search) {
                    $gq->where('group_name', 'like', "%{$search}%");
                })
                ->orWhereHas('school_section', function ($secQ) use ($search) {
                    $secQ->where('section_name', 'like', "%{$search}%");
                });
            });
        }

        $subjects = $query->latest()->paginate(10);

        if ($subjects->total() == 0) {
            return response()->json([
                'message' => 'No subject found for "' . $request->search . '"',
                'data' => [],
                'total' => 0
            ]);
        }

        return response()->json($subjects);
    }

    public function getGradingSystems()
    {
        $school = $this->getSchool();
        if (!$school) return response()->json(['data' => []]);

        $gradings = SchoolExamGrade::where('school_id', $school->id)
            ->select('full_mark', DB::raw('MIN(id) as id'))
            ->groupBy('full_mark')
            ->get();

        return response()->json(['data' => $gradings]);
    }
}