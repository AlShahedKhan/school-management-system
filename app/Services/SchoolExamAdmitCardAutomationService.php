<?php

namespace App\Services;

use App\Models\AdmissionStudent;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\SchoolExamAdmitCard;
use App\Models\SchoolExamName;
use App\Models\SchoolExamRoutine;
use App\Models\SchoolGroup;
use App\Models\SchoolSection;
use App\Models\SchoolSession;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SchoolExamAdmitCardAutomationService
{

    public function generateForStudent(AdmissionStudent $student, ?Carbon $today = null): int
    {
        if (! $this->isEligibleStudent($student)) {
            return 0;
        }

        $schoolId = School::where('user_id', $student->school_id)->value('id');
        if (! $schoolId) {
            return 0;
        }

        $placement = $this->resolvePlacement($student, $schoolId);
        if (! $placement) {
            return 0;
        }

        $currentDate = ($today ?: now())->toDateString();
        $exams = SchoolExamName::query()
            ->where('school_id', $schoolId)
            ->where('class_id', $placement['class']->id)
            ->where('session_id', $placement['session']->id)

            ->whereDate('exam_end_date', '>=', $currentDate)
            ->where(function ($query) use ($placement) {
                $query->where('group_id', $placement['group']?->id);

                if ($placement['group']) {
                    $query->orWhereNull('group_id');
                }
            })
            ->where(function ($query) use ($placement) {
                $query->where('section_id', $placement['section']->id);

                if ($placement['section']) {
                    $query->orWhereNull('section_id');
                }
            })
            ->get();

        $created = 0;

        foreach ($exams as $exam) {
            if (! $this->hasMatchingRoutine($schoolId, $placement, $exam)) {
                continue;
            }

            if ($this->createAdmitCard($schoolId, $student, $placement, $exam)) {
                $created++;
            }
        }

        return $created;
    }

    private function isEligibleStudent(AdmissionStudent $student): bool
    {
        return in_array(strtolower((string) $student->status), ['active', 'approved'], true)
            && $student->class_id
            && $student->session_id
            && $student->student_id_number;
    }

    private function resolvePlacement(AdmissionStudent $student, int $schoolId): ?array
    {
        $class = SchoolClass::where('school_id', $schoolId)
            ->where(fn ($query) => $query->where('id', $student->class_id)->orWhere('class_name', $student->class_id))
            ->first();

        if (! $class) {
            return null;
        }

        $group = null;
        if ($student->group_id !== null && $student->group_id !== '') {
            $group = SchoolGroup::where('school_id', $schoolId)
                ->where('class_id', $class->id)
                ->where(fn ($query) => $query->where('id', $student->group_id)->orWhere('group_name', $student->group_id))
                ->first();
        }

        $sectionQuery = SchoolSection::where('school_id', $schoolId)
            ->where('class_id', $class->id);
        if ($group) {
            $sectionQuery->where('group_id', $group->id);
        }
        $section = $sectionQuery
            ->where(fn ($query) => $query->where('id', $student->section_id)->orWhere('section_name', $student->section_id))
            ->first();

        $sessionQuery = SchoolSession::where('school_id', $schoolId)
            ->where('class_id', $class->id);
        if ($group) {
            $sessionQuery->where('group_id', $group->id);
        }
        if ($section) {
            $sessionQuery->where('section_id', $section->id);
        }
        $session = $sessionQuery
            ->where(fn ($query) => $query->where('id', $student->session_id)->orWhere('session_year', $student->session_id))
            ->first();

        if (! $section || ! $session) {
            return null;
        }

        return compact('class', 'group', 'section', 'session');
    }

    private function hasMatchingRoutine(int $schoolId, array $placement, SchoolExamName $exam): bool
    {
        return SchoolExamRoutine::query()
            ->where('school_id', $schoolId)
            ->where('class_id', $placement['class']->id)
            ->where('session_id', $placement['session']->id)
            ->where('exam_id', $exam->id)
            ->where(function ($query) use ($placement) {
                $query->where('group_id', $placement['group']?->id);

                if ($placement['group']) {
                    $query->orWhereNull('group_id');
                }
            })
            ->where(function ($query) use ($placement) {
                $query->where('section_id', $placement['section']->id);

                if ($placement['section']) {
                    $query->orWhereNull('section_id');
                }
            })
            ->exists();
    }

    private function createAdmitCard(int $schoolId, AdmissionStudent $student, array $placement, SchoolExamName $exam): bool
    {
        try {
            return (bool) DB::transaction(function () use ($schoolId, $student, $placement, $exam) {
                DB::statement("SELECT GET_LOCK('school_exam_admit_card_seq', 15)");

                try {
                    $className = $placement['class']->class_name;
                    $groupName = $placement['group']?->group_name;
                    $sectionName = $placement['section']->section_name;
                    $sessionName = $placement['session']->session_year;

                    $alreadyExists = SchoolExamAdmitCard::query()
                        ->where('school_id', $schoolId)
                        ->where('class_name', $className)
                        ->where('group_name', $groupName)
                        ->where('section_name', $sectionName)
                        ->where('session_name', $sessionName)
                        ->where('exam_name', $exam->exam_name)
                        ->where('student_id_number', $student->student_id_number)
                        ->exists();

                    if ($alreadyExists) {
                        return false;
                    }

                    $maxNumber = SchoolExamAdmitCard::max('admit_card_number');
                    $nextNumber = $maxNumber ? (int) $maxNumber + 1 : 24951080;

                    SchoolExamAdmitCard::create([
                        'school_id' => $schoolId,
                        'class_name' => $className,
                        'group_name' => $groupName,
                        'section_name' => $sectionName,
                        'session_name' => $sessionName,
                        'exam_name' => $exam->exam_name,
                        'student_id_number' => $student->student_id_number,
                        'admit_card_number' => $nextNumber,
                    ]);

                    return true;
                } finally {
                    DB::statement("SELECT RELEASE_LOCK('school_exam_admit_card_seq')");
                }
            });
        } catch (\Throwable $exception) {
            Log::error('Automatic admit card generation failed.', [
                'student_id' => $student->id,
                'exam_id' => $exam->id,
                'error' => $exception->getMessage(),
            ]);

            return false;
        }
    }
}
