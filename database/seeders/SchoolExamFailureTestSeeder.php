<?php

namespace Database\Seeders;

use App\Models\AdmissionStudent;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\SchoolExamGrade;
use App\Models\SchoolExamMark;
use App\Models\SchoolGroup;
use App\Models\SchoolSection;
use App\Models\SchoolSession;
use App\Models\SchoolSubject;
use Illuminate\Database\Seeder;

class SchoolExamFailureTestSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::query()->first();
        if (!$school) {
            $this->command?->warn('Failure test data was not seeded because no school exists.');

            return;
        }

        $class = SchoolClass::where('school_id', $school->id)->first();
        $group = $class
            ? SchoolGroup::where('school_id', $school->id)->where('class_id', $class->id)->first()
            : null;
        $section = $group
            ? SchoolSection::where('school_id', $school->id)
                ->where('class_id', $class->id)
                ->where('group_id', $group->id)
                ->first()
            : null;

        if (!$class || !$group || !$section) {
            $this->command?->warn('Failure test data was not seeded. A class, group, and section are required.');

            return;
        }

        $session = SchoolSession::where('school_id', $school->id)
            ->where('class_id', $class->id)
            ->where('group_id', $group->id)
            ->where('section_id', $section->id)
            ->first();

        $gradeDefinitions = [
            ['name' => 'A+', 'point' => 5, 'from' => 90, 'to' => 100],
            ['name' => 'A', 'point' => 4, 'from' => 80, 'to' => 89],
            ['name' => 'B', 'point' => 3, 'from' => 70, 'to' => 79],
            ['name' => 'C', 'point' => 2, 'from' => 60, 'to' => 69],
            ['name' => 'D', 'point' => 1, 'from' => 40, 'to' => 59],
            ['name' => 'F', 'point' => 0, 'from' => 0, 'to' => 39],
        ];

        $grades = collect($gradeDefinitions)->mapWithKeys(function (array $definition) use ($school) {
            return [
                $definition['name'] => SchoolExamGrade::updateOrCreate(
                    [
                        'school_id' => $school->id,
                        'full_mark' => 100,
                        'grade_name' => $definition['name'],
                    ],
                    [
                        'grade_point' => $definition['point'],
                        'mark_from' => $definition['from'],
                        'mark_to' => $definition['to'],
                    ]
                ),
            ];
        });

        $subject = SchoolSubject::updateOrCreate(
            [
                'school_id' => $school->id,
                'class_id' => $class->id,
                'group_id' => $group->id,
                'section_id' => $section->id,
                'subject_name' => 'Failure Logic Test Subject',
            ],
            [
                'grade_id' => $grades['A+']->id,
                'subject_code' => 'FAIL-TEST',
                'marks' => ['full_mark' => 100],
                'fail_mark' => 40,
                'subject_type' => 'Theory',
            ]
        );

        $subject->load(['school_class', 'school_group', 'school_section']);

        $students = AdmissionStudent::where('status', '!=', 'Inactive')
            ->where(function ($query) use ($school, $class, $group, $section) {
                $query->where('school_id', $school->id)
                    ->where('class_id', $class->id)
                    ->where('group_id', $group->id)
                    ->where('section_id', $section->id);
            })
            ->take(10)
            ->get();

        if ($students->count() < 10) {
            $students = $students->concat(
                AdmissionStudent::where('status', '!=', 'Inactive')
                    ->whereNotIn('student_id_number', $students->pluck('student_id_number'))
                    ->take(10 - $students->count())
                    ->get()
            );
        }

        if ($students->count() < 10) {
            $this->command?->warn('Failure test data was not seeded because 10 active students are required.');

            return;
        }

        $testResults = [
            ['mark' => 95, 'grade' => 'A+'],
            ['mark' => 85, 'grade' => 'A'],
            ['mark' => 75, 'grade' => 'B'],
            ['mark' => 65, 'grade' => 'C'],
            ['mark' => 55, 'grade' => 'D'],
            ['mark' => 39, 'grade' => 'F'],
            ['mark' => 92, 'grade' => 'A+'],
            ['mark' => 78, 'grade' => 'B'],
            ['mark' => 42, 'grade' => 'D'],
            ['mark' => 20, 'grade' => 'F'],
        ];

        foreach ($testResults as $index => $result) {
            $student = $students[$index];
            $grade = $grades[$result['grade']];

            SchoolExamMark::updateOrCreate(
                [
                    'school_id' => $school->id,
                    'class_name' => $subject->school_class->class_name,
                    'group_name' => $subject->school_group->group_name,
                    'section_name' => $subject->school_section->section_name,
                    'session_name' => (string) ($session?->session_year ?? now()->year),
                    'exam_name' => 'Failure Logic Test',
                    'subject_name' => $subject->subject_name,
                    'student_id_number' => $student->student_id_number,
                ],
                [
                    'student_name' => $student->student_name,
                    'roll_no' => $student->roll_no,
                    'mark' => $result['mark'],
                    'letter_name' => $grade->grade_name,
                    'point' => $grade->grade_point,
                    'status' => 'published',
                ]
            );
        }

        $this->command?->info('Seeded 10 grading test records: A+, A, B, C, D, and F results.');
    }
}
