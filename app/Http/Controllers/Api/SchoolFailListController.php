<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\SchoolExamMark;
use App\Models\SchoolExamName;
use App\Models\SchoolExamSchedule;
use App\Models\SchoolGroup;
use App\Models\SchoolSection;
use App\Models\SchoolSession;
use App\Models\SchoolSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SchoolFailListController extends Controller
{
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

        $class = SchoolClass::where('school_id', $school->id)->find($validated['class_id']);
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
            return response()->json(['message' => 'This exam result has not been published yet.'], 403);
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

        $subjects = SchoolSubject::query()
            ->where('school_id', $school->id)
            ->where('class_id', $class->id)
            ->where('group_id', $group->id)
            ->where('section_id', $section->id)
            ->whereIn('subject_name', $marks->pluck('subject_name')->filter()->unique())
            ->with('grade_type:id,full_mark,mark_from,grade_point,grade_name')
            ->get()
            ->keyBy('subject_name');

        $failedStudents = $marks->groupBy('student_id_number')
            ->map(function ($studentMarks) use ($subjects) {
                $first = $studentMarks->first();
                $failedSubjects = $studentMarks->filter(function ($mark) use ($subjects) {
                    $subject = $subjects->get($mark->subject_name);
                    $minimum = $this->minimumPassingMark($subject);

                    return (float) $mark->mark < $minimum;
                })->map(function ($mark) use ($subjects) {
                    $subject = $subjects->get($mark->subject_name);
                    $minimum = $this->minimumPassingMark($subject);

                    return [
                        'name' => $mark->subject_name ?: 'Unnamed subject',
                        'mark' => (float) $mark->mark,
                        'required_to_pass' => max(0, $minimum - (float) $mark->mark),
                    ];
                })->values();

                if ($failedSubjects->isEmpty()) {
                    return null;
                }

                return [
                    'student_id' => $first->student_id_number,
                    'student_name' => $first->student_name ?: 'N/A',
                    'failed_subjects' => $failedSubjects->count(),
                    'subject_details' => $failedSubjects->all(),
                ];
            })
            ->filter()
            ->sort(function (array $left, array $right) {
                $failedComparison = $right['failed_subjects'] <=> $left['failed_subjects'];

                return $failedComparison !== 0
                    ? $failedComparison
                    : strcmp((string) $left['student_id'], (string) $right['student_id']);
            })
            ->values();

        if ($validated['sort_order'] === 'last') {
            $failedStudents = $failedStudents->reverse()->values();
        }

        $studentCount = $failedStudents->count();
        $rows = $failedStudents->map(function (array $student, int $index) use (
            $validated,
            $class,
            $group,
            $section,
            $session,
            $exam,
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
                'failed_subjects' => $student['failed_subjects'],
                'subject_details' => $student['subject_details'],
            ];
        })->values();

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

    private function minimumPassingMark(?SchoolSubject $subject): float
    {
        if (is_numeric($subject?->fail_mark) && (float) $subject->fail_mark > 0) {
            return (float) $subject->fail_mark;
        }

        return (float) ($subject?->grade_type?->mark_from ?? 0);
    }
}
