<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\SchoolExamName;
use App\Models\SchoolExamMark;
use App\Models\SchoolExamSchedule;
use App\Models\SchoolGroup;
use App\Models\SchoolSection;
use App\Models\SchoolSession;
use App\Models\SchoolSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class SchoolMeritListController extends Controller
{
    public function exportPdf(Request $request)
    {
        $request->merge(['format' => 'pdf']);

        return $this->generate($request);
    }

    public function generate(Request $request)
    {
        $school = School::where('user_id', Auth::id())->first();

        if (! $school) {
            return response()->json(['message' => 'School context not found.'], 404);
        }

        $validated = $request->validate([
            'class_id' => ['required', 'integer'],
            'group_id' => ['required', 'integer'],
            'section_id' => ['required', 'integer'],
            'session_id' => ['required', 'integer'],
            'exam_id' => ['required', 'integer'],
            'sort_order' => ['required', 'in:first,last'],
        ]);

        $class = SchoolClass::where('school_id', $school->id)
            ->find($validated['class_id']);
        $group = SchoolGroup::where('school_id', $school->id)
            ->where('class_id', $class?->id)
            ->find($validated['group_id']);
        $section = SchoolSection::where('school_id', $school->id)
            ->where('class_id', $class?->id)
            ->where('group_id', $group?->id)
            ->find($validated['section_id']);
        $session = SchoolSession::where('school_id', $school->id)
            ->where('class_id', $class?->id)
            ->where('group_id', $group?->id)
            ->where('section_id', $section?->id)
            ->find($validated['session_id']);
        $exam = SchoolExamName::where('school_id', $school->id)
            ->where('class_id', $class?->id)
            ->where('group_id', $group?->id)
            ->where('section_id', $section?->id)
            ->where('session_id', $session?->id)
            ->find($validated['exam_id']);

        if (! $class || ! $group || ! $section || ! $session || ! $exam) {
            return response()->json([
                'message' => 'The selected class, group, section, session, or exam is invalid.',
            ], 422);
        }

        $schedule = SchoolExamSchedule::where('school_id', $school->id)
            ->where('class_name', $class->class_name)
            ->where('group_name', $group->group_name)
            ->where('section_name', $section->section_name)
            ->where('session_name', (string) $session->session_year)
            ->where('exam_name', $exam->exam_name)
            ->latest('id')
            ->first();

        if (! $schedule || $schedule->status !== 'Published') {
            return response()->json([
                'message' => 'This exam result has not been published yet.',
            ], 403);
        }

        $marks = SchoolExamMark::query()
            ->where('school_id', $school->id)
            ->where('class_name', $class->class_name)
            ->where('group_name', $group->group_name)
            ->where('section_name', $section->section_name)
            ->where('session_name', (string) $session->session_year)
            ->where('exam_name', $exam->exam_name)
            ->get();

        if ($marks->isEmpty()) {
            return response()->json(['message' => 'No marks found for this selection.'], 404);
        }

        $subjectNames = $marks->pluck('subject_name')->filter()->unique()->values();
        $subjects = SchoolSubject::query()
            ->where('school_id', $school->id)
            ->where('class_id', $class->id)
            ->where('group_id', $group->id)
            ->where('section_id', $section->id)
            ->whereIn('subject_name', $subjectNames)
            ->with('grade_type:id,full_mark')
            ->get()
            ->keyBy('subject_name');

        $missingFullMarks = $subjectNames->filter(function ($subjectName) use ($subjects) {
            $subject = $subjects->get($subjectName);
            $configuredMarks = is_array($subject?->marks) ? $subject->marks : [];
            $fullMark = $subject?->grade_type?->full_mark ?? ($configuredMarks['total_mark'] ?? null);

            return ! is_numeric($fullMark) || (float) $fullMark <= 0;
        })->values();

        if ($missingFullMarks->isNotEmpty()) {
            return response()->json([
                'message' => 'Full marks are not configured for: '.$missingFullMarks->implode(', ').'.',
            ], 422);
        }

        $totalMarks = $subjectNames->sum(function ($subjectName) use ($subjects) {
            $subject = $subjects->get($subjectName);
            $configuredMarks = is_array($subject?->marks) ? $subject->marks : [];

            return (float) ($subject?->grade_type?->full_mark ?? $configuredMarks['total_mark']);
        });

        $rankedStudents = $marks->groupBy('student_id_number')
            ->map(function ($studentMarks) {
                $first = $studentMarks->first();

                return [
                    'student_id' => $first->student_id_number,
                    'student_name' => $first->student_name ?: 'N/A',
                    'obtained_marks' => (float) $studentMarks->sum('mark'),
                ];
            })
            ->sort(function (array $left, array $right) {
                $marksComparison = $right['obtained_marks'] <=> $left['obtained_marks'];

                return $marksComparison !== 0
                    ? $marksComparison
                    : strcmp((string) $left['student_id'], (string) $right['student_id']);
            })
            ->values();

        $previousMarks = null;
        $previousRank = 0;
        $rankedStudents = $rankedStudents->map(function (array $student, int $index) use (&$previousMarks, &$previousRank) {
            if ($previousMarks === null || $student['obtained_marks'] !== $previousMarks) {
                $previousRank = $index + 1;
                $previousMarks = $student['obtained_marks'];
            }

            $student['merit_serial'] = $previousRank;

            return $student;
        });

        if ($validated['sort_order'] === 'last') {
            $rankedStudents = $rankedStudents->reverse()->values();
        }

        $studentCount = $rankedStudents->count();
        $rows = $rankedStudents->values()->map(function (array $student, int $index) use (
            $validated,
            $class,
            $group,
            $section,
            $session,
            $exam,
            $totalMarks,
            $studentCount
        ) {
            return [
                'sl' => $validated['sort_order'] === 'last' ? $studentCount - $index : $index + 1,
                'class' => $class->class_name,
                'group' => $group->group_name,
                'section' => $section->section_name,
                'session' => (string) $session->session_year,
                'exam' => $exam->exam_name,
                'student_id' => $student['student_id'],
                'student_name' => $student['student_name'],
                'total_marks' => $totalMarks,
                'obtained_marks' => $student['obtained_marks'],
                'merit_serial' => $student['merit_serial'],
            ];
        });

        if ($request->input('format') === 'pdf') {
            return Pdf::loadView('exports.merit_list_pdf', [
                'school' => $school,
                'filters' => [
                    'class' => $class->class_name,
                    'group' => $group->group_name,
                    'section' => $section->section_name,
                    'session' => (string) $session->session_year,
                    'exam' => $exam->exam_name,
                    'sort_order' => $validated['sort_order'],
                ],
                'rows' => $rows,
            ])->download('merit-list-'.now()->format('Ymd-His').'.pdf');
        }

        return response()->json([
            'filters' => [
                'class' => $class->class_name,
                'group' => $group->group_name,
                'section' => $section->section_name,
                'session' => (string) $session->session_year,
                'exam' => $exam->exam_name,
                'sort_order' => $validated['sort_order'],
            ],
            'rows' => $rows,
        ]);
    }
}
