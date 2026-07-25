<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SchoolRoutineRequest;
use App\Models\School;
use App\Models\SchoolRoutine;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SchoolRoutineController extends Controller
{
    private function getSchool($user)
    {
        if ($user->role === 'teacher') {
            $teacher = Teacher::where('id_number', $user->id_number)->first();
            if ($teacher) {
                return School::find($teacher->school_id);
            }
        }
        return School::where('user_id', $user->id)->first();
    }

    public function index(Request $request)
    {
        $school = $this->getSchool($request->user());
        if (!$school) return response()->json(['data' => [], 'total' => 0]);

        $query = SchoolRoutine::with(['school_class', 'school_group', 'school_section', 'school_subject', 'teacher'])
            ->where('school_id', $school->id);

        $user = $request->user();
        if ($user->role === 'teacher') {
            $teacher = Teacher::where('id_number', $user->id_number)->first();
            if ($teacher) {
                $query->where('teacher_id', $teacher->id);
            }
        }

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('group_id')) {
            $query->where('group_id', $request->group_id);
        }

        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('day_name', 'like', "%$s%")
                    ->orWhereHas('school_class', function ($c) use ($s) {
                        $c->where('class_name', 'like', "%$s%");
                    })
                    ->orWhereHas('teacher', function ($t) use ($s) {
                        $t->where('name', 'like', "%$s%");
                    });
            });
        }

        return response()->json($query->orderBy('day_name')->latest()->paginate(15));
    }

    public function store(SchoolRoutineRequest $request)
    {
        try {
            $school = $this->getSchool($request->user());
            if (!$school) return response()->json(['message' => 'School profile not found.'], 404);

            $routine = DB::transaction(function () use ($school, $request) {
                return SchoolRoutine::create(array_merge($request->validated(), [
                    'school_id' => $school->id,
                ]));
            });

            return response()->json(['message' => 'Routine created successfully', 'data' => $routine]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to create routine.', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $routine = SchoolRoutine::with(['school_class', 'school_group', 'school_section', 'school_subject', 'teacher'])->find($id);

        if (!$routine) {
            return response()->json(['message' => 'Routine not found'], 404);
        }

        return response()->json($routine);
    }

    public function update(SchoolRoutineRequest $request, $id)
    {
        try {
            DB::transaction(function () use ($id, $request) {
                $routine = SchoolRoutine::findOrFail($id);
                $routine->update($request->validated());
            });

            return response()->json(['message' => 'Routine updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update routine.', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
                SchoolRoutine::findOrFail($id)->delete();
            });

            return response()->json(['message' => 'Deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete routine.', 'error' => $e->getMessage()], 500);
        }
    }

    public function routineFilter(Request $request)
    {
        $school = $this->getSchool($request->user());
        if (!$school) {
            return response()->json([
                'message' => 'School profile not found.',
                'data' => []
            ], 404);
        }

        $query = SchoolRoutine::with([
            'school_class',
            'school_group',
            'school_section',
            'school_subject',
            'teacher'
        ])->where('school_id', $school->id);

        $user = $request->user();
        if ($user->role === 'teacher') {
            $teacher = Teacher::where('id_number', $user->id_number)->first();
            if ($teacher) {
                $query->where('teacher_id', $teacher->id);
            }
        }

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('group_id')) {
            $query->where('group_id', $request->group_id);
        }

        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        $data = $query->orderBy('day_name')->latest()->paginate(15);

        if ($data->total() == 0) {
            return response()->json([
                'status' => false,
                'message' => 'No routine found for selected filter.',
                'data' => []
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Routine Filter List',
            'data' => $data
        ]);
    }
}
