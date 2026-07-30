<?php

namespace App\Http\Controllers\Api;

use App\Exports\ExamGradeExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\SchoolExamGradeRequest;
use App\Models\School;
use App\Models\SchoolExamGrade;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class SchoolExamGradeController extends Controller
{
    private function getSchool()
    {
        return School::where('user_id', Auth::id())->first();
    }

    public function index(Request $request)
    {
        $school = $this->getSchool();
        if (!$school) {
            return response()->json(['data' => [], 'message' => 'School not found'], 404);
        }

        $query = SchoolExamGrade::where('school_id', $school->id);

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('grade_name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('grade_point', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('full_mark', 'LIKE', "%{$searchTerm}%");
            });
        }

        if ($request->filled('full_mark')) $query->where('full_mark', $request->full_mark);

        if ($request->has('all')) {
            return response()->json(['data' => $query->orderBy('full_mark', 'desc')->orderBy('mark_from', 'desc')->get()]);
        }

        $grades = $query->orderBy('full_mark', 'desc')
            ->orderBy('mark_from', 'desc')
            ->paginate($request->per_page ?? 10);

        return response()->json($grades);
    }

    public function store(SchoolExamGradeRequest $request)
    {
        $school = $this->getSchool();
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            foreach ($validated['grades'] as $gradeData) {
                $exists = SchoolExamGrade::where([
                    'school_id' => $school->id,
                    'full_mark' => $validated['full_mark'],
                    'grade_name' => $gradeData['grade_name'],
                ])->exists();

                if ($exists) continue;

                SchoolExamGrade::create([
                    'school_id' => $school->id,
                    'full_mark' => $validated['full_mark'],
                    'grade_name' => $gradeData['grade_name'],
                    'grade_point' => $gradeData['grade_point'],
                    'mark_from' => $gradeData['mark_from'],
                    'mark_to' => $gradeData['mark_to'],
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'Exam Grades created successfully']);
        } catch (\Throwable $error) {
            DB::rollBack();
            return response()->json(['message' => 'Error: ' . $error->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $school = $this->getSchool();
        $grade = SchoolExamGrade::where('school_id', $school->id)->findOrFail($id);
        return response()->json($grade);
    }

    public function update(SchoolExamGradeRequest $request, $id)
    {
        try {
            $school = $this->getSchool();
            $validated = $request->validated();

            $grade = DB::transaction(function () use ($school, $id, $validated) {
                $record = SchoolExamGrade::where('school_id', $school->id)->findOrFail($id);

                $exists = SchoolExamGrade::where([
                    'school_id' => $school->id,
                    'full_mark' => $validated['full_mark'],
                    'grade_name' => $validated['grade_name'],
                ])->where('id', '!=', $id)->exists();

                if ($exists) {
                    abort(422, 'Conflict: This grade name already exists for this full mark.');
                }

                $record->update([
                    'grade_name' => $validated['grade_name'],
                    'grade_point' => $validated['grade_point'],
                    'full_mark' => $record->full_mark,
                    'mark_from' => $validated['mark_from'],
                    'mark_to' => $validated['mark_to'],
                ]);

                return $record;
            });

            return response()->json(['message' => 'Grade updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage() ?: 'Failed to update grade.']);
        }
    }

    public function destroy($id)
    {
        try {
            $school = $this->getSchool();

            DB::transaction(function () use ($school, $id) {
                $grade = SchoolExamGrade::where('school_id', $school->id)->findOrFail($id);
                $grade->delete();
            });

            return response()->json(['message' => 'Grade deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage() ?: 'Failed to delete grade.'], 500);
        }
    }

    public function gpaThresholds()
    {
        $school = $this->getSchool();
        $points = SchoolExamGrade::where('school_id', $school->id)
                ->distinct()
                ->orderBy('grade_point', 'desc')
                ->pluck('grade_point');
        return response()->json(['data' => $points]);
    }

    public function export(Request $request)
    {
        $school = $this->getSchool();
        if (!$school) {
            return response()->json(['message' => 'School not found'], 404);
        }

        $query = SchoolExamGrade::where('school_id', $school->id);

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('grade_name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('grade_point', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('full_mark', 'LIKE', "%{$searchTerm}%");
            });
        }

        if ($request->filled('full_mark')) $query->where('full_mark', $request->full_mark);

        $grades = $query->orderBy('full_mark', 'desc')
            ->orderBy('mark_from', 'desc')
            ->get();

        $type = $request->query('type', 'pdf');

        if ($type === 'excel') {
            return Excel::download(new ExamGradeExport($grades), "exam_grades_" . now()->format('Ymd') . ".xlsx");
        }

        $pdf = Pdf::loadView('exports.exam_grade_list_pdf', [
            'grades' => $grades,
            'school' => $school,
            'date' => now()->format('j-F-Y')
        ]);

        return $pdf->download("exam_grades_" . now()->format('Ymd') . ".pdf");
    }
}
