<?php

namespace App\Http\Controllers\Api;

use App\Exports\ExamRoutineExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\SchoolExamRoutineRequest;
use App\Models\SchoolExamRoutine;
use App\Models\School;
use App\Models\Teacher;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class SchoolExamRoutineController extends Controller
{
    private function getSchool($user = null)
    {
        $user = $user ?? Auth::user();
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
        try {
            $school = $this->getSchool($request->user());
            if (!$school) {
                return response()->json(['data' => [], 'message' => 'School context not found'], 404);
            }

            $query = SchoolExamRoutine::with([
                'schoolClass', 'schoolGroup', 'schoolSection', 'schoolSession', 'schoolExam', 'schoolSubject'
            ])->where('school_id', $school->id);

            $filterMap = [
                'class_id' => 'class_id',
                'group_id' => 'group_id',
                'section_id' => 'section_id',
                'session_id' => 'session_id',
                'exam_id' => 'exam_id',
                'subject_id' => 'subject_id',
            ];

            foreach ($filterMap as $inputKey => $column) {
                if ($request->filled($inputKey)) {
                    $query->where($column, $request->input($inputKey));
                }
            }

            if ($request->filled('search')) {
                $s = $request->search;
                $query->where(function ($q) use ($s) {
                    $q->where('exam_date', 'like', "%$s%")
                        ->orWhere('day_name', 'like', "%$s%")
                        ->orWhereHas('schoolSubject', function ($sub) use ($s) {
                            $sub->where('subject_name', 'like', "%$s%");
                        });
                });
            }

            $perPage = $request->input('per_page', 15);
            $routines = $query->orderBy('exam_date', 'asc')
                ->orderBy('start_time', 'asc')
                ->paginate($perPage);

            return response()->json($routines);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to load exam routines.', 'error' => $e->getMessage()], 500);
        }
    }

    public function store(SchoolExamRoutineRequest $request)
    {
        try {
            $school = $this->getSchool();
            if (!$school) {
                return response()->json(['message' => 'Unauthorized school context.'], 403);
            }

            return DB::transaction(function () use ($request, $school) {
                $data = $request->validated();
                $data['school_id'] = $school->id;

                $routine = SchoolExamRoutine::create($data);

                return response()->json([
                    'message' => 'Exam Routine created successfully',
                    'data'    => $routine->load([
                        'schoolClass', 'schoolGroup', 'schoolSection', 'schoolSession', 'schoolExam', 'schoolSubject'
                    ])
                ], 201);
            });
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to create exam routine.', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $school = $this->getSchool();
            $routine = SchoolExamRoutine::where('school_id', $school->id)
                ->with(['schoolClass', 'schoolGroup', 'schoolSection', 'schoolSession', 'schoolExam', 'schoolSubject'])
                ->findOrFail($id);
            return response()->json($routine);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Exam routine not found.'], 404);
        }
    }

    public function update(SchoolExamRoutineRequest $request, $id)
    {
        try {
            $school = $this->getSchool();
            if (!$school) {
                return response()->json(['message' => 'Unauthorized school context.'], 403);
            }

            return DB::transaction(function () use ($request, $id, $school) {
                $routine = SchoolExamRoutine::where('school_id', $school->id)->findOrFail($id);
                $routine->update($request->validated());

                return response()->json([
                    'message' => 'Exam Routine updated successfully',
                    'data'    => $routine->load([
                        'schoolClass', 'schoolGroup', 'schoolSection', 'schoolSession', 'schoolExam', 'schoolSubject'
                    ])
                ]);
            });
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update exam routine.', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $school = $this->getSchool();
            $routine = SchoolExamRoutine::where('school_id', $school->id)->findOrFail($id);

            DB::transaction(function () use ($routine) {
                $routine->delete();
            });

            return response()->json(['message' => 'Exam Routine deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete exam routine.', 'error' => $e->getMessage()], 500);
        }
    }

    public function export(Request $request)
    {
        try {
            $school = $this->getSchool();
            if (!$school) {
                return response()->json(['message' => 'School not found'], 404);
            }

            $query = SchoolExamRoutine::with([
                'schoolClass', 'schoolGroup', 'schoolSection', 'schoolSession', 'schoolExam', 'schoolSubject'
            ])->where('school_id', $school->id);

            $filterMap = [
                'class_id' => 'class_id',
                'group_id' => 'group_id',
                'section_id' => 'section_id',
                'session_id' => 'session_id',
                'exam_id' => 'exam_id',
                'subject_id' => 'subject_id',
            ];

            foreach ($filterMap as $inputKey => $column) {
                if ($request->filled($inputKey)) {
                    $query->where($column, $request->input($inputKey));
                }
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->whereHas('schoolSubject', function ($sub) use ($search) {
                        $sub->where('subject_name', 'like', '%' . $search . '%');
                    })->orWhere('exam_date', 'like', '%' . $search . '%')
                        ->orWhere('day_name', 'like', '%' . $search . '%');
                });
            }

            $records = $query->orderBy('exam_date', 'asc')->orderBy('start_time', 'asc')->get();
            $type = $request->query('type', 'pdf');

            if ($type === 'excel') {
                return Excel::download(new ExamRoutineExport($records), 'exam_routines_' . now()->format('Ymd') . '.xlsx');
            }

            $pdf = Pdf::loadView('exports.exam_routine_list_pdf', [
                'records' => $records,
                'school' => $school,
                'date' => now()->format('j-F-Y')
            ]);

            return $pdf->download('exam_routines_' . now()->format('Ymd') . '.pdf');
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to export exam routines.', 'error' => $e->getMessage()], 500);
        }
    }
}
