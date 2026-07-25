<?php

namespace Database\Seeders;

use App\Models\Donate;
use App\Models\School;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DonateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $school = School::first();

        if (! $school) {
            $this->command->warn('No school found. DonateSeeder skipped.');
            return;
        }

        $locations = [
            'Dhaka',
            'Chattogram',
            'Khulna',
            'Rajshahi',
            'Sylhet',
            'Barishal',
            'Rangpur',
            'Mymensingh',
            'Gazipur',
            'Narayanganj',
        ];

        $reasons = [
            'Poor Student Support',
            'School Development',
            'Library Development',
            'Classroom Renovation',
            'Computer Lab',
            'Science Lab',
            'Sports Equipment',
            'Scholarship Fund',
            'Annual Program',
            'Educational Materials',
        ];

        $firstNames = [
            'Md. Rahim',
            'Karim',
            'Hasan',
            'Sabbir',
            'Nayeem',
            'Tanvir',
            'Rakib',
            'Sohel',
            'Fahim',
            'Jahid',
            'Arif',
            'Rasel',
            'Shakil',
            'Nafis',
            'Jamil',
        ];

        $lastNames = [
            'Ahmed',
            'Islam',
            'Hossain',
            'Rahman',
            'Khan',
            'Mia',
            'Sarker',
            'Ali',
            'Uddin',
            'Biswas',
        ];

        for ($i = 1; $i <= 50; $i++) {
            Donate::create([
                'school_id'      => $school->id,
                'donate_no'      => 'DON-' . now()->format('Y') . '-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'name'           => $firstNames[array_rand($firstNames)] . ' ' . $lastNames[array_rand($lastNames)],
                'mobile_number'  => '01' . random_int(300000000, 999999999),
                'location'       => $locations[array_rand($locations)],
                'donate_reason'  => $reasons[array_rand($reasons)],
                'amount'         => random_int(1000, 50000),
                'note'           => fake()->optional()->sentence(),
                'created_at'     => fake()->dateTimeBetween('-2 years', 'now'),
                'updated_at'     => now(),
            ]);
        }
    }
}