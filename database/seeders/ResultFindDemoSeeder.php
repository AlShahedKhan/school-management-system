<?php

namespace Database\Seeders;

use App\Models\AdmissionStudent;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\SchoolExamGrade;
use App\Models\SchoolExamMark;
use App\Models\SchoolExamName;
use App\Models\SchoolExamSchedule;
use App\Models\SchoolGroup;
use App\Models\SchoolSection;
use App\Models\SchoolSession;
use App\Models\SchoolSubject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ResultFindDemoSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::query()->where('school_name', 'Green Valley School')->first();
        if (! $school) {
            $this->command?->warn('Result demo was not seeded because Green Valley School does not exist. Run RoleUserSeeder first.');

            return;
        }

        $class = SchoolClass::query()->where('school_id', $school->id)->where('class_name', 'Play')->firstOrFail();
        $group = SchoolGroup::query()->where('school_id', $school->id)->where('class_id', $class->id)->where('group_name', 'General')->firstOrFail();
        $section = SchoolSection::query()->where('school_id', $school->id)->where('class_id', $class->id)->where('group_id', $group->id)->where('section_name', 'A')->firstOrFail();
        $session = SchoolSession::query()->where('school_id', $school->id)->where('class_id', $class->id)->where('group_id', $group->id)->where('section_id', $section->id)->where('session_year', '2026')->firstOrFail();
        $students = AdmissionStudent::query()
            ->where('school_id', $school->user_id)
            ->where('class_id', $class->id)
            ->where('group_id', $group->id)
            ->where('section_id', $section->id)
            ->where('status', '!=', 'Inactive')
            ->orderBy('id')
            ->take(20)
            ->get();

        $demoStudentNames = [
            'Nusrat Jahan Maria',
            'Ashifa Jahan',
            'Daliya Akter',
            'Lima Akter',
            'Fatema Akter',
            'Sumaiya Akter Jim',
            'Lamiya Akter',
            'Ebha Akter',
            'Nusrat Jahan Noon',
            'Sosmeta Ghosh',
            'Niha Akter',
            'Samia Akter',
            'Rafsan Ahmed',
            'Sadia Islam',
            'Ayman Hossain',
            'Mehedi Hasan',
            'Tanjim Rahman',
            'Mim Akter',
            'Arif Hossain',
            'Jannatul Ferdous',
        ];

        $templateStudent = $students->firstOrFail();
        for ($index = $students->count() + 1; $index <= 20; $index++) {
            $students->push(AdmissionStudent::updateOrCreate(
                ['student_id_number' => '02499'.str_pad((string) $index, 6, '0', STR_PAD_LEFT)],
                [
                    'school_id' => $school->user_id,
                    'division' => 'Dhaka',
                    'district' => 'Dhaka',
                    'upazila' => 'Dhanmondi',
                    'school' => $school->school_name,
                    'class_id' => $class->id,
                    'group_id' => $group->id,
                    'section_id' => $section->id,
                    'session_id' => $session->id,
                    'admission_fee' => $templateStudent->admission_fee ?? '1500',
                    'admission_date' => now()->toDateString(),
                    'previous_school' => 'Previous School '.$index,
                    'previous_class' => $class->class_name,
                    'previous_group' => $group->group_name,
                    'previous_section' => $section->section_name,
                    'previous_session' => '2025',
                    'interview_code' => 'RESULT-DEMO-'.$index,
                    'last_exam_result' => 'Passed',
                    'guardian_id' => $templateStudent->guardian_id,
                    'student_name' => $demoStudentNames[$index - 1] ?? 'Student '.$index,
                    'father_name' => 'Mohammad '.($demoStudentNames[$index - 1] ?? 'Guardian').' Father',
                    'mother_name' => 'Rahima '.($demoStudentNames[$index - 1] ?? 'Guardian').' Mother',
                    'current_division' => 'Dhaka',
                    'current_district' => 'Dhaka',
                    'current_upazila' => 'Dhanmondi',
                    'current_village' => 'Green Road',
                    'permanent_division' => 'Dhaka',
                    'permanent_district' => 'Dhaka',
                    'permanent_upazila' => 'Dhanmondi',
                    'permanent_village' => 'Green Road',
                    'mobile' => '57777'.str_pad((string) $index, 6, '0', STR_PAD_LEFT),
                    'password' => $templateStudent->password,
                    'status' => 'Active',
                    'active_date' => now()->toDateString(),
                    'status_updated_by' => $school->user_id,
                ]
            ));
        }

        $primaryStudent = $students->first();

        $gradeDefinitions = [
            ['grade_name' => 'A+', 'grade_point' => 5, 'mark_from' => 80, 'mark_to' => 100],
            ['grade_name' => 'A', 'grade_point' => 4, 'mark_from' => 70, 'mark_to' => 79],
            ['grade_name' => 'A-', 'grade_point' => 3.5, 'mark_from' => 60, 'mark_to' => 69],
            ['grade_name' => 'B', 'grade_point' => 3, 'mark_from' => 50, 'mark_to' => 59],
            ['grade_name' => 'C', 'grade_point' => 2, 'mark_from' => 40, 'mark_to' => 49],
            ['grade_name' => 'D', 'grade_point' => 1, 'mark_from' => 33, 'mark_to' => 39],
            ['grade_name' => 'F', 'grade_point' => 0, 'mark_from' => 0, 'mark_to' => 32],
        ];

        // Remove the malformed legacy F-range row created by an earlier demo seed.
        SchoolExamGrade::where('school_id', $school->id)
            ->where('full_mark', 100)
            ->where('mark_from', 23)
            ->where('mark_to', 32)
            ->where('grade_name', '32')
            ->delete();

        $grades = collect($gradeDefinitions)->mapWithKeys(function (array $definition) use ($school) {
            return [$definition['grade_name'] => SchoolExamGrade::updateOrCreate(
                [
                    'school_id' => $school->id,
                    'full_mark' => 100,
                    'grade_name' => $definition['grade_name'],
                ],
                [...$definition, 'full_mark' => 100]
            )];
        });

        $gradeDefinitions50 = [
            ['grade_name' => 'A+', 'grade_point' => 5, 'mark_from' => 40, 'mark_to' => 50],
            ['grade_name' => 'A', 'grade_point' => 4, 'mark_from' => 35, 'mark_to' => 39],
            ['grade_name' => 'A-', 'grade_point' => 3.5, 'mark_from' => 30, 'mark_to' => 34],
            ['grade_name' => 'B', 'grade_point' => 3, 'mark_from' => 25, 'mark_to' => 29],
            ['grade_name' => 'C', 'grade_point' => 2, 'mark_from' => 20, 'mark_to' => 24],
            ['grade_name' => 'D', 'grade_point' => 1, 'mark_from' => 17, 'mark_to' => 19],
            ['grade_name' => 'F', 'grade_point' => 0, 'mark_from' => 0, 'mark_to' => 16],
        ];

        foreach ($gradeDefinitions50 as $definition) {
            SchoolExamGrade::updateOrCreate(
                [
                    'school_id' => $school->id,
                    'full_mark' => 50,
                    'grade_name' => $definition['grade_name'],
                ],
                [...$definition, 'full_mark' => 50]
            );
        }

        $subjects = [
            ['name' => 'Bangla 1st', 'mark' => 82.4, 'tutorial' => 16, 'mcq' => 30, 'writing' => 36.4, 'practical' => 0],
            ['name' => 'Bangla 2nd', 'mark' => 84.6, 'tutorial' => 18, 'mcq' => 30, 'writing' => 36.6, 'practical' => 0],
            ['name' => 'English 1st', 'mark' => 88.2, 'tutorial' => 18, 'mcq' => 30, 'writing' => 40.2, 'practical' => 0],
            ['name' => 'English 2nd', 'mark' => 81.2, 'tutorial' => 14, 'mcq' => 30, 'writing' => 37.2, 'practical' => 0],
            ['name' => 'Mathematics', 'mark' => 82.4, 'tutorial' => 20, 'mcq' => 30, 'writing' => 32.4, 'practical' => 0],
            ['name' => 'General Science', 'mark' => 83.6, 'tutorial' => 18, 'mcq' => 30, 'writing' => 35.6, 'practical' => 0, 'failure_marks' => [20, 23, 26, 29, 32]],
            ['name' => 'Physics', 'mark' => 81.5, 'tutorial' => 18, 'mcq' => 30, 'writing' => 33.5, 'practical' => 0, 'failure_marks' => [22, 25, 27, 30, 32]],
            ['name' => 'Chemistry', 'mark' => 80.8, 'tutorial' => 17, 'mcq' => 30, 'writing' => 33.8, 'practical' => 0, 'failure_marks' => [24, 26, 28, 31, 32]],
            ['name' => 'Bangladesh & Global Studies', 'mark' => 88.7, 'tutorial' => 18, 'mcq' => 30, 'writing' => 40.7, 'practical' => 0],
            ['name' => 'Religion & Moral Education', 'mark' => 88, 'tutorial' => 18, 'mcq' => 30, 'writing' => 40, 'practical' => 0],
            ['name' => 'ICT', 'mark' => 43.8, 'tutorial' => 8, 'mcq' => 15, 'writing' => 20.8, 'practical' => 0],
            ['name' => 'Agriculture Studies', 'mark' => 44.2, 'tutorial' => 9, 'mcq' => 15, 'writing' => 20.2, 'practical' => 0],
            ['name' => 'Discipline', 'mark' => 48, 'tutorial' => 18, 'mcq' => 0, 'writing' => 30, 'practical' => 0],
            ['name' => 'Higher Mathematics', 'mark' => 86.5, 'tutorial' => 18, 'mcq' => 30, 'writing' => 38.5, 'practical' => 0],
            ['name' => 'Biology', 'mark' => 84.8, 'tutorial' => 17, 'mcq' => 30, 'writing' => 37.8, 'practical' => 0],
            ['name' => 'Civics and Citizenship', 'mark' => 87.3, 'tutorial' => 18, 'mcq' => 30, 'writing' => 39.3, 'practical' => 0],
            ['name' => 'Career Education', 'mark' => 91.2, 'tutorial' => 20, 'mcq' => 30, 'writing' => 41.2, 'practical' => 0],
        ];

        // Keep the demo exam deterministic when the seeder is run repeatedly.
        SchoolExamMark::where('school_id', $school->id)
            ->where('exam_name', 'Result Design Demo')
            ->delete();

        SchoolExamMark::where('school_id', $school->id)
            ->where('exam_name', 'Result Design Demo')
            ->where('subject_name', 'Fail List Demo')
            ->delete();

        SchoolExamMark::where('school_id', $school->id)
            ->where('exam_name', 'Result Design Demo')
            ->where('subject_name', 'Science')
            ->delete();

        SchoolSubject::where('school_id', $school->id)
            ->where('class_id', $class->id)
            ->where('group_id', $group->id)
            ->where('section_id', $section->id)
            ->where('subject_name', 'Fail List Demo')
            ->delete();

        foreach ($subjects as $subjectData) {
            $grade = $grades->first(function (SchoolExamGrade $item) use ($subjectData) {
                return $subjectData['mark'] >= $item->mark_from && $subjectData['mark'] <= $item->mark_to;
            }) ?? $grades['F'];

            SchoolSubject::updateOrCreate(
                [
                    'school_id' => $school->id,
                    'class_id' => $class->id,
                    'group_id' => $group->id,
                    'section_id' => $section->id,
                    'subject_name' => $subjectData['name'],
                ],
                [
                    'grade_id' => $grades['A+']->id,
                    'subject_code' => 'DEMO-'.strtoupper(substr(md5($subjectData['name']), 0, 6)),
                    'marks' => ['total_mark' => 100, 'tutorial_mark' => 20, 'mcq_mark' => 30, 'writing_mark' => 50, 'practical_mark' => 0],
                    'fail_mark' => 33,
                    'subject_type' => 'Theory',
                ]
            );

            foreach ($students as $studentIndex => $student) {
                $studentMark = ($subjectData['failure_marks'] ?? [])[$studentIndex]
                    ?? round(max(0, $subjectData['mark'] - ($studentIndex * 0.5)), 2);
                $writingMark = round(max(0, $studentMark - $subjectData['tutorial'] - $subjectData['mcq'] - $subjectData['practical']), 2);
                $studentGrade = $grades->first(function (SchoolExamGrade $item) use ($studentMark) {
                    return $studentMark >= $item->mark_from && $studentMark <= $item->mark_to;
                }) ?? $grades['F'];

                SchoolExamMark::updateOrCreate(
                    [
                        'school_id' => $school->id,
                        'exam_name' => 'Result Design Demo',
                        'subject_name' => $subjectData['name'],
                        'student_id_number' => $student->student_id_number,
                    ],
                    [
                        'class_name' => $class->class_name,
                        'group_name' => $group->group_name,
                        'section_name' => $section->section_name,
                        'session_name' => (string) $session->session_year,
                        'student_name' => $student->student_name,
                        'roll_no' => $student->roll_no,
                        'mark' => $studentMark,
                        'tutorial_mark' => $subjectData['tutorial'],
                        'mcq_mark' => $subjectData['mcq'],
                        'writing_mark' => $writingMark,
                        'theory_mark' => $writingMark,
                        'practical_mark' => $subjectData['practical'],
                        'letter_name' => $studentGrade->grade_name,
                        'point' => $studentGrade->grade_point,
                        'status' => 'published',
                    ]
                );
            }
        }

        SchoolExamName::updateOrCreate(
            [
                'school_id' => $school->id,
                'class_id' => $class->id,
                'group_id' => $group->id,
                'section_id' => $section->id,
                'session_id' => $session->id,
                'exam_name' => 'Result Design Demo',
            ]
        );

        SchoolExamSchedule::updateOrCreate(
            [
                'school_id' => $school->id,
                'class_name' => $class->class_name,
                'group_name' => $group->group_name,
                'section_name' => $section->section_name,
                'session_name' => (string) $session->session_year,
                'exam_name' => 'Result Design Demo',
            ],
            [
                'total_subject' => count($subjects),
                'submitted_subject' => count($subjects),
                'remaining_subject' => 0,
                'publish_date' => now()->toDateString(),
                'publish_time' => now()->subMinute()->format('H:i:s'),
                'status' => 'Published',
                'published_at' => now(),
                'created_by' => $school->user_id,
            ]
        );

        DB::table('school_exam_admit_cards')->updateOrInsert(
            ['admit_card_number' => 900001],
            [
                'school_id' => $school->id,
                'class_name' => $class->class_name,
                'group_name' => $group->group_name,
                'section_name' => $section->section_name,
                'session_name' => (string) $session->session_year,
                'exam_name' => 'Result Design Demo',
                'student_id_number' => $students->first()->student_id_number,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        $this->command?->info('Result demo seeded successfully.');
        $this->command?->info("Students seeded: {$students->count()}");
        $this->command?->info("Primary Student ID: {$primaryStudent->student_id_number}");
        $this->command?->info('Admit Card: 900001');
        $this->command?->info('Classwise Exam: Play / General / A / 2026 / Result Design Demo');
    }
}
