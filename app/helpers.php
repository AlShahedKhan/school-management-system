<?php

use App\Support\PublicTranslationResolver;

if (! function_exists('public_trans')) {
    function public_trans(string $key, array $replace = [], ?string $locale = null): string
    {
        return app(PublicTranslationResolver::class)->get($key, $replace, $locale);
    }
}

if (! function_exists('bn_number')) {
    function bn_number(string|int|float|null $value, ?string $locale = null): string
    {
        $locale ??= app()->getLocale();
        $string = (string) ($value ?? '');

        if ($locale !== 'bn') {
            return $string;
        }

        return strtr($string, [
            '0' => '০',
            '1' => '১',
            '2' => '২',
            '3' => '৩',
            '4' => '৪',
            '5' => '৫',
            '6' => '৬',
            '7' => '৭',
            '8' => '৮',
            '9' => '৯',
        ]);
    }
}

if (! function_exists('generate_school_common_next_serial')) {
    /**
     * Get the next integer serial number for a school's ID generation.
     */
    function generate_school_common_next_serial($schoolId): int
    {
        $school = \App\Models\School::find($schoolId) ?? \App\Models\School::where('user_id', $schoolId)->first();
        if (!$school) {
            throw new \Exception("School profile not found for school_id: {$schoolId}");
        }

        $schoolUser = \App\Models\User::find($school->user_id);
        $schoolPrefix = $schoolUser?->id_number ? substr($schoolUser->id_number, -5) : '00000';

        $lastStudent = \App\Models\AdmissionStudent::where('school_id', $school->user_id)
            ->where('student_id_number', 'LIKE', $schoolPrefix . '%')
            ->orderByRaw('CAST(RIGHT(student_id_number, 6) AS UNSIGNED) DESC')
            ->first();
        $lastStudentSerial = $lastStudent?->student_id_number ? (int) substr($lastStudent->student_id_number, -6) : 0;

        $lastTeacher = \App\Models\Teacher::where('school_id', $school->id)
            ->where('id_number', 'LIKE', $schoolPrefix . '%')
            ->orderByRaw('CAST(RIGHT(id_number, 6) AS UNSIGNED) DESC')
            ->first();
        $lastTeacherSerial = $lastTeacher?->id_number ? (int) substr($lastTeacher->id_number, -6) : 0;

        $lastUser = \App\Models\User::where('id_number', 'LIKE', $schoolPrefix . '%')
            ->orderByRaw('CAST(RIGHT(id_number, 6) AS UNSIGNED) DESC')
            ->first();
        $lastUserSerial = $lastUser?->id_number ? (int) substr($lastUser->id_number, -6) : 0;

        return max($lastStudentSerial, $lastTeacherSerial, $lastUserSerial) + 1;
    }
}

if (! function_exists('generate_school_common_id_number')) {
    /**
     * Generate common sequential 11-digit ID for students and teachers.
     * Added on 2026-07-11
     */
    function generate_school_common_id_number($schoolId): string
    {
        $school = \App\Models\School::find($schoolId) ?? \App\Models\School::where('user_id', $schoolId)->first();
        if (!$school) {
            throw new \Exception("School profile not found for school_id: {$schoolId}");
        }

        $schoolUser = \App\Models\User::find($school->user_id);
        $schoolPrefix = $schoolUser?->id_number ? substr($schoolUser->id_number, -5) : '00000';

        $nextSerial = generate_school_common_next_serial($schoolId);

        return $schoolPrefix . str_pad($nextSerial, 6, '0', STR_PAD_LEFT);
    }
}