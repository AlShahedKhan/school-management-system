<?php

namespace Database\Seeders;

use App\Enums\EmployeeStatusEnum;
use App\Models\Employee;
use App\Models\School;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::first();
        if (! $school) {
            $this->command->warn('No school found. EmployeeSeeder skipped.');
            return;
        }
        $designations = [
            'Principal',
            'Vice Principal',
            'Head Teacher',
            'Assistant Teacher',
            'Teacher',
            'Accountant',
            'Office Assistant',
            'Office Executive',
            'Computer Operator',
            'Librarian',
            'Lab Assistant',
            'Security Guard',
            'Cleaner',
            'Driver',
            'Peon',
        ];
        $firstNames = [
            'Md. Rahim',
            'Karim',
            'Hasan',
            'Jamal',
            'Nayeem',
            'Sabbir',
            'Tanvir',
            'Rakib',
            'Sohel',
            'Rasel',
            'Arif',
            'Shakil',
            'Jahid',
            'Nafis',
            'Fahim',
        ];
        $lastNames = [
            'Ahmed',
            'Islam',
            'Rahman',
            'Hossain',
            'Khan',
            'Ali',
            'Mia',
            'Sarker',
            'Biswas',
            'Uddin',
        ];
        for ($i = 1; $i <= 50; $i++) {
            Employee::create([
                'school_id'         => $school->id,
                'employee_no'       => 'EMP-' . now()->format('Y') . '-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'name'              => $firstNames[array_rand($firstNames)] . ' ' . $lastNames[array_rand($lastNames)],
                'mobile_number'     => '01' . random_int(300000000, 999999999),
                'designation'       => $designations[array_rand($designations)],
                'salary_amount'     => random_int(12000, 60000),
                'salary_start_date' => fake()->dateTimeBetween('-3 years', '-1 month')->format('Y-m-d'),
                'employee_status'   => fake()->randomElement(EmployeeStatusEnum::values()),
                'note'              => fake()->optional()->sentence(),
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        }
    }
}