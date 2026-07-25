<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->packages() as $package) {
            $totalPayable = $package['student_limit'] * $package['per_student_price'];
            $afterDiscount = $totalPayable - ($totalPayable * ($package['annual_discount_percent'] / 100));

            Package::query()->updateOrCreate(
                ['package_type' => $package['package_type']],
                [
                    'student_limit' => $package['student_limit'],
                    'teacher_limit' => $package['teacher_limit'],
                    'free_trial_days' => $package['free_trial_days'],
                    'per_student_price' => $package['per_student_price'],
                    'total_payable' => $totalPayable,
                    'annual_discount_percent' => $package['annual_discount_percent'],
                    'after_discount' => $afterDiscount,
                    'sms_limit' => $package['sms_limit'],
                    'is_active' => true,
                ]
            );
        }
    }

    private function packages(): array
    {
        return [
            [
                'package_type' => 'Basic',
                'student_limit' => 200,
                'teacher_limit' => 10,
                'free_trial_days' => 14,
                'per_student_price' => 10,
                'annual_discount_percent' => 0,
                'sms_limit' => 500,
            ],
            [
                'package_type' => 'Standard',
                'student_limit' => 500,
                'teacher_limit' => 25,
                'free_trial_days' => 14,
                'per_student_price' => 8,
                'annual_discount_percent' => 10,
                'sms_limit' => 1500,
            ],
            [
                'package_type' => 'Premium',
                'student_limit' => 1000,
                'teacher_limit' => 50,
                'free_trial_days' => 30,
                'per_student_price' => 6,
                'annual_discount_percent' => 15,
                'sms_limit' => 5000,
            ],
            [
                'package_type' => 'Advance',
                'student_limit' => 2000,
                'teacher_limit' => 100,
                'free_trial_days' => 30,
                'per_student_price' => 5,
                'annual_discount_percent' => 20,
                'sms_limit' => 10000,
            ],
        ];
    }
}
