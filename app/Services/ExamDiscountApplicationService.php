<?php

namespace App\Services;

use App\Models\AdmissionStudent;
use App\Models\SchoolExamGrade;
use App\Models\SchoolExamMark;

class ExamDiscountApplicationService
{
    public function studentQualifies(int $schoolId, int $studentId, ?string $minimumGrade): bool
    {
        if (!$minimumGrade) {
            return false;
        }

        $student = AdmissionStudent::find($studentId);
        if (!$student || empty($student->student_id_number)) {
            return false;
        }

        $marksQuery = SchoolExamMark::query()
            ->where('school_id', $schoolId)
            ->where('student_id_number', $student->student_id_number)
            ->where('status', 'published');

        $avgMark  = (float) $marksQuery->avg('mark');
        $avgPoint = (float) $marksQuery->avg('point');

        $grade = $this->resolveMinimumGrade($schoolId, $minimumGrade);



        if ($grade && $grade->mark_from !== null && $grade->mark_from !== '') {
            return $avgMark > 0 && $avgMark >= (float) $grade->mark_from;
        }


        $minPoint = $grade && $grade->grade_point !== null && $grade->grade_point !== ''
            ? (float) $grade->grade_point
            : $this->defaultGradePoint($minimumGrade);

        return $minPoint !== null && $avgPoint > 0 && $avgPoint >= $minPoint;
    }

    private function resolveMinimumGrade(int $schoolId, string $minimumGrade): ?SchoolExamGrade
    {
        return SchoolExamGrade::query()
            ->where('school_id', $schoolId)
            ->where('grade_name', $minimumGrade)
            ->orderByDesc('grade_point')
            ->first();
    }

    private function defaultGradePoint(string $gradeName): ?float
    {
        $normalized = strtoupper(trim($gradeName));

        return match ($normalized) {
            'A+'           => 5.0,
            'A'            => 4.0,
            'A-'           => 3.5,
            'B'            => 3.0,
            'C'            => 2.0,
            'D'            => 1.0,
            'F'            => 0.0,
            default        => null,
        };
    }
}
