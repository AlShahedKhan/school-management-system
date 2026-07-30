<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SchoolExamMark;
use App\Models\School;
use App\Models\SchoolExamGrade;
use App\Models\SchoolSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SchoolExamMarkSubmitController extends Controller
{
    private function calculateGrade(int $schoolId, Request $request, float $mark): array
    {
        $subjectQuery = SchoolSubject::where('school_id', $schoolId)
            ->where('subject_name', $request->subject_name);

        if ($request->filled('class_name')) {
            $subjectQuery->whereHas('school_class', fn ($query) =>
                $query->where('class_name', $request->class_name));
        }
        if ($request->filled('group_name')) {
            $subjectQuery->whereHas('school_group', fn ($query) =>
                $query->where('group_name', $request->group_name));
        }
        if ($request->filled('section_name')) {
            $subjectQuery->whereHas('school_section', fn ($query) =>
                $query->where('section_name', $request->section_name));
        }

        $subject = $subjectQuery->first()
            ?? SchoolSubject::where('school_id', $schoolId)
                ->where('subject_name', $request->subject_name)
                ->first();

        $gradeReference = $subject?->grade_id
            ? SchoolExamGrade::where('school_id', $schoolId)->find($subject->grade_id)
            : null;

        $rules = SchoolExamGrade::where('school_id', $schoolId)
            ->when($gradeReference, fn ($query) => $query->where('full_mark', $gradeReference->full_mark))
            ->get();

        $minimumPassingMark = (float) ($subject?->fail_mark ?? 0);
        if ($minimumPassingMark <= 0) {
            $minimumPassingMark = (float) ($rules
                ->filter(fn ($rule) => (float) $rule->grade_point > 0
                    && !in_array(strtolower(trim((string) $rule->grade_name)), ['f', 'fail', 'failed'], true))
                ->min('mark_from') ?? 0);
        }

        if ($mark < $minimumPassingMark) {
            return ['letter_name' => 'F', 'point' => 0];
        }

        $matchedRule = $rules->first(fn ($rule) =>
            $mark >= (float) $rule->mark_from && $mark <= (float) $rule->mark_to);

        return $matchedRule
            ? ['letter_name' => $matchedRule->grade_name, 'point' => $matchedRule->grade_point]
            : ['letter_name' => 'F', 'point' => 0];
    }

    public function index(Request $request)
    {
        $school = School::where('user_id', Auth::id())->first();
        $query = SchoolExamMark::where('school_id', $school->id);

        if ($request->class_name) $query->where('class_name', $request->class_name);
        if ($request->exam_name) $query->where('exam_name', $request->exam_name);
        if ($request->subject_name) $query->where('subject_name', $request->subject_name);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('student_name', 'LIKE', "%{$request->search}%")
                    ->orWhere('student_id_number', 'LIKE', "%{$request->search}%");
            });
        }

        return response()->json($query->orderBy('id', 'desc')->paginate(15));
    }

    public function store(Request $request)
    {
        $school = School::where('user_id', Auth::id())->first();

        return DB::transaction(function () use ($request, $school) {
            foreach ($request->marks_data as $data) {
                $mark = (float) ($data['mark'] ?? 0);
                $grade = $this->calculateGrade($school->id, $request, $mark);
                // Check if entry already exists for this student in this specific exam/subject
                $exists = SchoolExamMark::where([
                    'school_id' => $school->id,
                    'class_name' => $request->class_name,
                    'session_name' => $request->session_name,
                    'exam_name' => $request->exam_name,
                    'subject_name' => $request->subject_name,
                    'student_id_number' => $data['student_id_number']
                ])->exists();

                if ($exists) {
                    return response()->json([
                        'status' => 'exists',
                        'message' => "Mark already exists for Student ID: {$data['student_id_number']}. Delete the old entry to re-submit."
                    ], 422);
                }

                // Modified on 2026-07-09: Prevent entering marks for inactive students
                $student = \App\Models\AdmissionStudent::where('student_id_number', $data['student_id_number'])->first();
                if ($student && $student->status === 'Inactive') {
                    return response()->json([
                        'status' => 'inactive',
                        'message' => "Student ID: {$data['student_id_number']} is inactive. Cannot submit marks for inactive students."
                    ], 422);
                }

                SchoolExamMark::create([
                    'school_id' => $school->id,
                    'class_name' => $request->class_name,
                    'group_name' => $request->group_name,
                    'section_name' => $request->section_name,
                    'session_name' => $request->session_name,
                    'exam_name' => $request->exam_name,
                    'subject_name' => $request->subject_name,
                    'student_id_number' => $data['student_id_number'],
                    'student_name' => $data['student_name'],
                    'roll_no' => $data['roll_no'] ?? null,
                    'mark' => $mark,
                    'theory_mark' => $data['theory_mark'] ?? 0,
                    'practical_mark' => $data['practical_mark'] ?? 0,
                    'letter_name' => $grade['letter_name'],
                    'point' => $grade['point'],
                    'status' => $request->status ?? 'published'
                ]);
            }

            return response()->json(['message' => 'All marks processed successfully!']);
        });
    }

    public function show($id)
    {
        $school = School::where('user_id', Auth::id())->first();
        $mark = SchoolExamMark::where('school_id', $school->id)->findOrFail($id);
        return response()->json($mark);
    }

    public function update(Request $request, $id)
    {
        $school = School::where('user_id', Auth::id())->first();
        $mark = SchoolExamMark::where('school_id', $school->id)->findOrFail($id);

        // Update with the first item in marks_data (since update handles 1 record)
        $data = $request->marks_data[0];
        $markValue = (float) ($data['mark'] ?? 0);
        $grade = $this->calculateGrade($school->id, $request, $markValue);

        $mark->update([
            'class_name' => $request->class_name,
            'group_name' => $request->group_name,
            'section_name' => $request->section_name,
            'session_name' => $request->session_name,
            'exam_name' => $request->exam_name,
            'subject_name' => $request->subject_name,
            'mark' => $markValue,
            'theory_mark' => $data['theory_mark'] ?? 0,
            'practical_mark' => $data['practical_mark'] ?? 0,
            'letter_name' => $grade['letter_name'],
            'point' => $grade['point'],
            'status' => $request->status ?? $mark->status
        ]);

        return response()->json(['message' => 'Mark updated successfully']);
    }

    public function destroy($id)
    {
        $school = School::where('user_id', Auth::id())->first();
        SchoolExamMark::where('school_id', $school->id)->findOrFail($id)->delete();
        return response()->json(['message' => 'Record deleted']);
    }
}
