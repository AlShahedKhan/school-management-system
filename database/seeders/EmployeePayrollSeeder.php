<?php

namespace Database\Seeders;

use App\Enums\PaymentMethodEnum;
use App\Enums\SalaryTypeEnum;
use App\Models\Employee;
use App\Models\EmployeePayroll;
use Illuminate\Database\Seeder;

class EmployeePayrollSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::all();
        if ($employees->isEmpty()) {
            $this->command->warn('No employees found. Please run EmployeeSeeder first.');
            return;
        }
        foreach ($employees as $employee) {
            for ($i = 0; $i < 5; $i++) {
                $date = now()->subMonths($i);
                $salary = (float) $employee->salary_amount;
                $status = fake()->randomElement([
                    SalaryTypeEnum::PAID,
                    SalaryTypeEnum::PARTIAL_PAID,
                    SalaryTypeEnum::DUE_PAID,
                ]);
                $receiveAmount = match ($status) {
                    SalaryTypeEnum::PAID => $salary,
                    SalaryTypeEnum::PARTIAL_PAID => fake()->numberBetween(
                        (int) ($salary * 0.30),
                        (int) ($salary * 0.90)
                    ),
                    SalaryTypeEnum::DUE_PAID => fake()->numberBetween(
                        500,
                        (int) ($salary * 0.40)
                    ),
                };
                $paymentMethod = fake()->randomElement([
                    PaymentMethodEnum::CASH,
                    PaymentMethodEnum::BANK,
                ]);
                EmployeePayroll::create([
                    'school_id'        => $employee->school_id,
                    'employee_id'      => $employee->id,
                    'salary_type'      => $status->value,
                    'receive_amount'   => $receiveAmount,
                    'receive_month'    => $date->month,
                    'receive_year'     => $date->year,
                    'receive_date'     => fake()->dateTimeBetween(
                        $date->copy()->startOfMonth(),
                        $date->copy()->endOfMonth()
                    ),
                    'payment_method'   => $paymentMethod->value,
                    'bank_name' => $paymentMethod === PaymentMethodEnum::BANK
                        ? fake()->randomElement([
                            'Dutch-Bangla Bank',
                            'BRAC Bank',
                            'Islami Bank',
                            'City Bank',
                            'Sonali Bank',
                            'Eastern Bank',
                        ])
                        : null,
                    'transaction_id' => $paymentMethod === PaymentMethodEnum::BANK
                        ? strtoupper(fake()->bothify('TXN####??'))
                        : null,
                    'note' => fake()->optional()->sentence(),
                ]);
            }
        }
    }
}