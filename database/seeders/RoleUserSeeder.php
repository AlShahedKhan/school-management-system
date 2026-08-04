<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AdmissionStudent;
use App\Models\Guardian;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\SchoolEmployee;
use App\Models\SchoolExamName;
use App\Models\SchoolGroup;
use App\Models\SchoolSection;
use App\Models\SchoolSession;
use App\Models\SchoolStudentFee;
use App\Models\StudentPromotion;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class RoleUserSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | ADMIN
        |--------------------------------------------------------------------------
        */
        User::updateOrCreate(
            ['email' => 'asthaitlimited@gmail.com'],
            [
                'role' => 'admin',
                'name' => 'Super Admin',
                'school_name' => null,
                'mobile' => '01942845813',
                'id_number' => 'ADMIN001',
                'password' => Hash::make('12345678admin'),
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | SCHOOL
        |--------------------------------------------------------------------------
        */
        $schoolUser = User::updateOrCreate(
            ['email' => 'school@test.com'],
            [
                'role' => 'school',
                'name' => 'Green Valley School',
                'school_name' => 'Green Valley School',
                'mobile' => '2222222222',
                'id_number' => '00002401',
                'password' => Hash::make('123456'),
            ]
        );

        $school = School::firstOrNew(['user_id' => $schoolUser->id]);
        $school->school_name = 'Green Valley School';
        $school->division = $school->division ?: 'Dhaka';
        $school->district = $school->district ?: 'Dhaka';
        $school->upazila = $school->upazila ?: 'Dhanmondi';
        $school->village = $school->village ?: 'Green Road';
        $school->id_number = 'SCHOOL001';
        $school->eiin_number = $school->eiin_number ?: '123456';
        $school->mobile = '2222222222';
        $school->email = 'school@test.com';
        $school->approval_status = 'approved';
        $school->approved_at = now();
        $school->save();

        /*
        |--------------------------------------------------------------------------
        | CLASSES
        |--------------------------------------------------------------------------
        */
        $classes = [
            'Play',
            'Nursery',
            'One',
            'Two',
            'Three',
            'Four',
            'Five',
            'Six',
            'Seven',
            'Eight',
        ];

        foreach ($classes as $className) {
            SchoolClass::updateOrCreate(
                [
                    'school_id' => $school->id,
                    'class_name' => $className,
                ],
            );
        }

        $seededClasses = SchoolClass::where('school_id', $school->id)
            ->whereIn('class_name', $classes)
            ->orderBy('id')
            ->get()
            ->keyBy('class_name');

        $seededSections = collect();
        $seededSessions = collect();

        foreach ($seededClasses as $class) {
            $group = SchoolGroup::updateOrCreate(
                [
                    'school_id' => $school->id,
                    'class_id' => $class->id,
                    'group_name' => 'General',
                ]
            );

            $section = SchoolSection::updateOrCreate(
                [
                    'school_id' => $school->id,
                    'class_id' => $class->id,
                    'section_name' => 'A',
                ],
                [
                    'group_id' => $group->id,
                ]
            );

            $session = SchoolSession::updateOrCreate(
                [
                    'school_id' => $school->id,
                    'class_id' => $class->id,
                    'section_id' => $section->id,
                    'session_year' => '2026',
                ],
                [
                    'group_id' => $group->id,
                    'start_date' => '2026-01-01',
                    'end_date' => '2026-12-31',
                    'total_days' => 365,
                    'remaining_days' => max(now()->diffInDays(Carbon::create(2026, 12, 31), false), 0),
                    'fees_due' => 0,
                ]
            );

            $seededSections->put($class->class_name, $section);
            $seededSessions->put($class->class_name, $session);
        }

        /*
        |--------------------------------------------------------------------------
        | STUDENTS
        |--------------------------------------------------------------------------
        */
        $studentFirstNames = [
            'Rahim',
            'Karim',
            'Sadia',
            'Nusrat',
            'Ayman',
            'Mehedi',
            'Tanvir',
            'Jannat',
            'Fahim',
            'Mim',
            'Rafi',
            'Sumaiya',
        ];
        $studentLastNames = ['Islam', 'Ahmed', 'Hasan', 'Akter', 'Hossain'];
        $studentStatuses = ['Active', 'Active', 'Active', 'approved', 'pending', 'Inactive'];

        for ($index = 1; $index <= 60; $index++) {
            $number = str_pad((string) $index, 3, '0', STR_PAD_LEFT);
            $className = $classes[($index - 1) % count($classes)];
            $status = $studentStatuses[($index - 1) % count($studentStatuses)];
            $studentName = $studentFirstNames[($index - 1) % count($studentFirstNames)].' '.$studentLastNames[($index - 1) % count($studentLastNames)];
            $studentIdNumber = '02401'.str_pad((string) $index, 6, '0', STR_PAD_LEFT);
            $mobile = '55555'.str_pad((string) $index, 6, '0', STR_PAD_LEFT);
            $class = $seededClasses->get($className);
            $section = $seededSections->get($className);
            $session = $seededSessions->get($className);

            $guardian = Guardian::updateOrCreate(
                ['mobile' => '66666'.str_pad((string) $index, 6, '0', STR_PAD_LEFT)],
                [
                    'name' => 'Guardian '.$number,
                    'relation' => $index % 2 === 0 ? 'Mother' : 'Father',
                    'division' => 'Dhaka',
                    'district' => 'Dhaka',
                    'upazila' => 'Dhanmondi',
                    'village' => 'Green Road',
                ]
            );

            $student = AdmissionStudent::updateOrCreate(
                ['student_id_number' => $studentIdNumber],
                [
                    'school_id' => $schoolUser->id,
                    'division' => 'Dhaka',
                    'district' => 'Dhaka',
                    'upazila' => 'Dhanmondi',
                    'school' => $school->school_name,
                    'class_id' => $class?->id,
                    'group_id' => $section?->group_id,
                    'section_id' => $section?->id,
                    'session_id' => $session?->id,
                    'admission_fee' => '1500',
                    'admission_date' => Carbon::now()->subDays($index)->toDateString(),
                    'previous_school' => 'Previous School '.$number,
                    'previous_class' => $className,
                    'previous_group' => 'General',
                    'previous_section' => 'A',
                    'previous_session' => '2025',
                    'interview_code' => 'INT'.$number,
                    'last_exam_result' => 'Passed',
                    'guardian_id' => $guardian->id,
                    'student_name' => $studentName,
                    'father_name' => 'Father '.$number,
                    'mother_name' => 'Mother '.$number,
                    'current_division' => 'Dhaka',
                    'current_district' => 'Dhaka',
                    'current_upazila' => 'Dhanmondi',
                    'current_village' => 'Green Road',
                    'permanent_division' => 'Dhaka',
                    'permanent_district' => 'Dhaka',
                    'permanent_upazila' => 'Dhanmondi',
                    'permanent_village' => 'Green Road',
                    'mobile' => $mobile,
                    'password' => Hash::make('123456'),
                    'status' => $status,
                    'inactive_date' => $status === 'Inactive' ? Carbon::now()->subDays(7)->toDateString() : null,
                    'inactive_reason' => $status === 'Inactive' ? 'Seeded inactive record for dashboard testing.' : null,
                    'active_date' => in_array($status, ['Active', 'approved'], true) ? Carbon::now()->subDays(30)->toDateString() : null,
                    'status_updated_by' => $schoolUser->id,
                ]
            );

            User::updateOrCreate(
                ['id_number' => $student->student_id_number],
                [
                    'role' => 'student',
                    'name' => $student->student_name,
                    'school_name' => $school->school_name,
                    'mobile' => $student->mobile,
                    'id_number' => $student->student_id_number,
                    'password' => Hash::make('123456'),
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | EMPLOYEES
        |--------------------------------------------------------------------------
        */
        $employees = [
            ['employee_name' => 'Abdul Karim', 'designation' => 'Accountant', 'mobile_number' => '77777000001', 'salary_amount' => 28000],
            ['employee_name' => 'Nadia Akter', 'designation' => 'Office Assistant', 'mobile_number' => '77777000002', 'salary_amount' => 22000],
            ['employee_name' => 'Hasan Mahmud', 'designation' => 'Librarian', 'mobile_number' => '77777000003', 'salary_amount' => 24000],
            ['employee_name' => 'Rokeya Begum', 'designation' => 'Receptionist', 'mobile_number' => '77777000004', 'salary_amount' => 21000],
            ['employee_name' => 'Jamal Uddin', 'designation' => 'Security Officer', 'mobile_number' => '77777000005', 'salary_amount' => 20000],
        ];

        foreach ($employees as $index => $employee) {
            SchoolEmployee::updateOrCreate(
                [
                    'school_id' => $school->id,
                    'mobile_number' => $employee['mobile_number'],
                ],
                [
                    'employee_name' => $employee['employee_name'],
                    'designation' => $employee['designation'],
                    'salary_amount' => $employee['salary_amount'],
                    'payroll_date' => Carbon::now()->startOfMonth()->addDays($index)->toDateString(),
                    'bank_name' => 'Demo Bank',
                    'branch' => 'Dhanmondi',
                    'routing_number' => '12026000'.$index,
                    'ac_holder_name' => $employee['employee_name'],
                    'ac_number' => '90002401'.str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT),
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | PROMOTIONS
        |--------------------------------------------------------------------------
        */
        $promotionStudents = AdmissionStudent::where('school_id', $schoolUser->id)
            ->whereIn('status', ['Active', 'approved'])
            ->orderBy('id')
            ->take(8)
            ->get();

        foreach ($promotionStudents as $index => $student) {
            $fromClassName = $classes[$index % max(count($classes) - 1, 1)];
            $toClassName = $classes[$index + 1];
            $fromClass = $seededClasses->get($fromClassName);
            $toClass = $seededClasses->get($toClassName);

            if (! $fromClass || ! $toClass) {
                continue;
            }

            StudentPromotion::updateOrCreate(
                [
                    'school_id' => $school->id,
                    'student_id' => $student->id,
                    'to_student_id_number' => $student->student_id_number.'-P'.str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                ],
                [
                    'from_class_id' => $fromClass->id,
                    'from_group_id' => null,
                    'from_section_id' => $seededSections->get($fromClassName)?->id,
                    'from_session_id' => $seededSessions->get($fromClassName)?->id,
                    'from_student_id_number' => $student->student_id_number,
                    'to_class_id' => $toClass->id,
                    'to_group_id' => null,
                    'to_section_id' => $seededSections->get($toClassName)?->id,
                    'to_session_id' => $seededSessions->get($toClassName)?->id,
                    'from_admission_id' => $student->admission_id ?? $student->student_id_number,
                    'to_admission_id' => ($student->admission_id ?? $student->student_id_number).'-P'.str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                    'promote_date' => Carbon::now()->subDays($index + 1)->toDateString(),
                    'promote_fee' => 1000 + ($index * 100),
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | TUITION FEES
        |--------------------------------------------------------------------------
        */
        $tuitionStudents = AdmissionStudent::where('school_id', $schoolUser->id)
            ->whereIn('status', ['Active', 'approved'])
            ->orderBy('id')
            ->take(24)
            ->get();

        foreach ($tuitionStudents as $index => $student) {
            $baseAmount = 2500 + (($index % 4) * 250);
            $paidAmount = $index % 3 === 0 ? $baseAmount : ($index % 3 === 1 ? $baseAmount / 2 : 0);
            $dueAmount = max($baseAmount - $paidAmount, 0);

            SchoolStudentFee::updateOrCreate(
                [
                    'school_id' => $school->id,
                    'student_id' => $student->id,
                    'fee_type_name' => 'Tuition',
                    'fee_name' => 'Monthly Tuition',
                    'generation_period' => now()->format('Y-m'),
                ],
                [
                    'fee_template_id' => null,
                    'fee_assign_id' => null,
                    'base_amount' => $baseAmount,
                    'discount_amount' => 0,
                    'payable_amount' => $baseAmount,
                    'paid_amount' => $paidAmount,
                    'due_amount' => $dueAmount,
                    'advance_amount' => 0,
                    'pay_date' => Carbon::now()->startOfMonth()->addDays(9)->toDateString(),
                    'due_date' => Carbon::now()->startOfMonth()->addDays(14)->toDateString(),
                    'status' => match (true) {
                        $dueAmount <= 0 => 'paid',
                        $paidAmount > 0 => 'partial_paid',
                        default => 'unpaid',
                    },
                ]
            );
        }

        $feeSeeds = [
            [
                'type' => 'Food',
                'name' => 'Monthly Food',
                'students' => $tuitionStudents->take(18),
                'amount' => 1800,
                'due_offset' => 16,
            ],
            [
                'type' => 'Fine',
                'name' => 'Late Payment Fine',
                'students' => $tuitionStudents->take(10),
                'amount' => 250,
                'due_offset' => -3,
            ],
            [
                'type' => 'Admission',
                'name' => 'Admission Fee',
                'students' => AdmissionStudent::where('school_id', $schoolUser->id)
                    ->whereIn('status', ['Active', 'approved', 'Inactive'])
                    ->orderBy('id')
                    ->take(20)
                    ->get(),
                'amount' => 1500,
                'due_offset' => 7,
            ],
        ];

        foreach ($feeSeeds as $feeSeed) {
            foreach ($feeSeed['students'] as $index => $student) {
                $baseAmount = $feeSeed['amount'] + (($index % 3) * 100);
                $paidAmount = match ($index % 4) {
                    0 => $baseAmount,
                    1 => $baseAmount / 2,
                    default => 0,
                };
                $dueAmount = max($baseAmount - $paidAmount, 0);
                $dueDate = Carbon::now()->startOfMonth()->addDays($feeSeed['due_offset']);

                SchoolStudentFee::updateOrCreate(
                    [
                        'school_id' => $school->id,
                        'student_id' => $student->id,
                        'fee_type_name' => $feeSeed['type'],
                        'fee_name' => $feeSeed['name'],
                        'generation_period' => now()->format('Y-m'),
                    ],
                    [
                        'fee_template_id' => null,
                        'fee_assign_id' => null,
                        'base_amount' => $baseAmount,
                        'discount_amount' => 0,
                        'payable_amount' => $baseAmount,
                        'paid_amount' => $paidAmount,
                        'due_amount' => $dueAmount,
                        'advance_amount' => 0,
                        'pay_date' => Carbon::now()->startOfMonth()->addDays(9)->toDateString(),
                        'due_date' => $dueDate->toDateString(),
                        'status' => match (true) {
                            $dueAmount <= 0 => 'paid',
                            $paidAmount > 0 => 'partial_paid',
                            $dueDate->isPast() => 'over_due',
                            default => 'unpaid',
                        },
                    ]
                );
            }
        }

        foreach ($promotionStudents as $index => $student) {
            $baseAmount = 1000 + ($index * 100);

            SchoolStudentFee::updateOrCreate(
                [
                    'school_id' => $school->id,
                    'student_id' => $student->id,
                    'fee_type_name' => 'Promote',
                    'fee_name' => 'Promotion Fee',
                    'generation_period' => now()->format('Y-m'),
                ],
                [
                    'fee_template_id' => null,
                    'fee_assign_id' => null,
                    'base_amount' => $baseAmount,
                    'discount_amount' => 0,
                    'payable_amount' => $baseAmount,
                    'paid_amount' => $index % 2 === 0 ? $baseAmount : 0,
                    'due_amount' => $index % 2 === 0 ? 0 : $baseAmount,
                    'advance_amount' => 0,
                    'pay_date' => Carbon::now()->startOfMonth()->addDays(10)->toDateString(),
                    'due_date' => Carbon::now()->startOfMonth()->addDays(18)->toDateString(),
                    'status' => $index % 2 === 0 ? 'paid' : 'unpaid',
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | EXAMS
        |--------------------------------------------------------------------------
        */
        $examNames = [
            'First Class Test',
            'Second Class Test',
            'Monthly Assessment',
            'Mid Term',
            'Half Yearly',
            'Pre-Test',
            'Model Test',
            'Final Exam',
            'Annual Exam',
            'Scholarship Test',
            'Practical Exam',
            'Oral Test',
            'Revision Test',
            'Mock Exam',
            'Board Preparation Test',
        ];

        foreach ($examNames as $index => $examName) {
            $className = $classes[$index % count($classes)];
            $class = $seededClasses->get($className);
            if (!$class) continue;

            SchoolExamName::updateOrCreate(
                [
                    'school_id' => $school->id,
                    'class_id' => $class->id,
                    'section_id' => $seededSections->get($className)?->id,
                    'session_id' => $seededSessions->get($className)?->id,
                    'exam_name' => $examName,
                ],
                [
                    'group_id' => null,
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | TEACHERS
        |--------------------------------------------------------------------------
        */
        $teachers = [
            [
                'name' => 'John Teacher',
                'designation' => 'Mathematics Teacher',
                'email' => 'teacher@test.com',
                'mobile' => '3333333333',
                'id_number' => 'TEACHER001',
                'password' => '123456',
            ],
            [
                'name' => 'Sarah Ahmed',
                'designation' => 'English Teacher',
                'email' => 'sarah.teacher@test.com',
                'mobile' => '3333333334',
                'id_number' => 'TEACHER002',
                'password' => '123456',
            ],
            [
                'name' => 'Mahmud Hasan',
                'designation' => 'Science Teacher',
                'email' => 'mahmud.teacher@test.com',
                'mobile' => '3333333335',
                'id_number' => 'TEACHER003',
                'password' => '123456',
            ],
        ];

        $subjects = [
            'Bangla Teacher',
            'English Teacher',
            'Mathematics Teacher',
            'Science Teacher',
            'Biology Teacher',
            'Chemistry Teacher',
            'Physics Teacher',
            'ICT Teacher',
            'Islamic Studies Teacher',
            'Social Science Teacher',
            'Geography Teacher',
            'History Teacher',
        ];

        for ($index = 4; $index <= 50; $index++) {
            $number = str_pad((string) $index, 3, '0', STR_PAD_LEFT);
            $subject = $subjects[($index - 4) % count($subjects)];

            $teachers[] = [
                'name' => "Seed Teacher {$number}",
                'designation' => $subject,
                'email' => "teacher{$number}@test.com",
                'mobile' => '3333333' . str_pad((string) $index, 3, '0', STR_PAD_LEFT),
                'id_number' => "TEACHER{$number}",
                'password' => '123456',
            ];
        }

        foreach ($teachers as $teacherData) {
            Teacher::updateOrCreate(
                ['id_number' => $teacherData['id_number']],
                [
                    'school_id' => $school->id,
                    'name' => $teacherData['name'],
                    'designation' => $teacherData['designation'],
                    'mobile' => $teacherData['mobile'],
                    'email' => $teacherData['email'],
                    'password' => $teacherData['password'],
                ]
            );

            User::updateOrCreate(
                ['id_number' => $teacherData['id_number']],
                [
                    'role' => 'teacher',
                    'name' => $teacherData['name'],
                    'school_name' => $school->school_name,
                    'email' => $teacherData['email'],
                    'mobile' => $teacherData['mobile'],
                    'id_number' => $teacherData['id_number'],
                    'password' => Hash::make($teacherData['password']),
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | STUDENT
        |--------------------------------------------------------------------------
        */
        User::updateOrCreate(
            ['email' => 'student@test.com'],
            [
                'role' => 'student',
                'name' => 'Rahim Student',
                'school_name' => 'Green Valley School',
                'mobile' => '01800000000',
                'id_number' => '02401000000',
                'password' => Hash::make('123456'),
            ]
        );
    }
}
