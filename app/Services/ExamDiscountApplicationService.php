<?php

namespace App\Services;

use App\Models\AdmissionStudent;
use Illuminate\Support\Facades\DB;

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

        $minPoint = $this->resolveMinimumPoint($schoolId, $minimumGrade);
        if ($minPoint === null) {
            return false;
        }

        $avgPoint = (float) DB::table('school_exam_marks')
            ->where('school_id', $schoolId)
            ->where('student_id_number', $student->student_id_number)
            ->where('status', 'published')
            ->avg('point');

        return $avgPoint > 0 && $avgPoint >= $minPoint;
    }

    private function resolveMinimumPoint(int $schoolId, string $minimumGrade): ?float
    {
        $grade = DB::table('school_exam_grades')
            ->where('school_id', $schoolId)
            ->where('grade_name', $minimumGrade)
            ->orderByDesc('grade_point')
            ->first();

        if ($grade && $grade->grade_point !== null && $grade->grade_point !== '') {
            return (float) $grade->grade_point;
        }

        return $this->defaultGradePoint($minimumGrade);
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
