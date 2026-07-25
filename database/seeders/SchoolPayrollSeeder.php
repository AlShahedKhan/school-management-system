<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\SchoolEmployee;
use App\Models\SchoolPayroll;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SchoolPayrollSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::query()
            ->where('email', 'school@test.com')
            ->orWhere('school_name', 'Green Valley School')
            ->first();

        if (! $school) {
            return;
        }

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
                    'payroll_date' => now()->startOfMonth()->addDays($index)->toDateString(),
                    'bank_name' => 'Demo Bank',
                    'branch' => 'Dhanmondi',
                    'routing_number' => '12026000'.$index,
                    'ac_holder_name' => $employee['employee_name'],
                    'ac_number' => '90002401'.str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT),
                ]
            );
        }

        $payrollMonths = collect(range(0, 3))
            ->map(fn (int $monthOffset) => now()->subMonthsNoOverflow($monthOffset)->startOfMonth())
            ->reverse()
            ->values();

        SchoolEmployee::query()
            ->where('school_id', $school->id)
            ->whereIn('mobile_number', collect($employees)->pluck('mobile_number'))
            ->orderBy('id')
            ->get()
            ->each(function (SchoolEmployee $employee, int $employeeIndex) use ($payrollMonths, $school): void {
                $payrollMonths->each(function (Carbon $payrollMonth, int $monthIndex) use ($employee, $employeeIndex, $school): void {
                    $totalPayable = (float) $employee->salary_amount;
                    $paidAmount = match (($employeeIndex + $monthIndex) % 4) {
                        0, 1 => $totalPayable,
                        2 => round($totalPayable * 0.60, 2),
                        default => 0,
                    };
                    $recordedAt = $payrollMonth->isSameMonth(now())
                        ? now()->subHours(2)
                        : $payrollMonth->copy()->addDays(24)->setTime(10, 0);

                    SchoolPayroll::updateOrCreate(
                        [
                            'school_id' => $school->id,
                            'school_employee_id' => $employee->id,
                            'month' => $payrollMonth->format('F'),
                            'year' => $payrollMonth->year,
                        ],
                        [
                            'employee_name' => $employee->employee_name,
                            'mobile_number' => $employee->mobile_number,
                            'designation' => $employee->designation,
                            'present' => 24 - (($employeeIndex + $monthIndex) % 3),
                            'absent' => ($employeeIndex + $monthIndex) % 2,
                            'leave' => $employeeIndex % 2,
                            'total_payable' => $totalPayable,
                            'payable_due' => max($totalPayable - $paidAmount, 0),
                            'advance_status' => 'No',
                            'pay_type' => $employeeIndex % 2 === 0 ? 'Bank' : 'Cash',
                            'paid_amount' => $paidAmount,
                            'created_at' => $recordedAt,
                            'updated_at' => $recordedAt,
                        ]
                    );
                });
            });
    }
}
