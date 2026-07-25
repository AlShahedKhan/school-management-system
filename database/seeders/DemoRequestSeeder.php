<?php

namespace Database\Seeders;

use App\Enums\DemoRequestStatus;
use App\Models\DemoRequest;
use Illuminate\Database\Seeder;

class DemoRequestSeeder extends Seeder
{
    public function run(): void
    {
        $faker = fake();

        $schools = [
            'Dhaka Model School',
            'Astha Grammar School',
            'Greenfield International School',
            'Bright Future Academy',
            'Sunrise Public School',
            'Maple Leaf High School',
            'Scholars Mission School',
            'Pioneer Learning Academy',
            'Harmony Residential School',
            'Scholastica View School',
        ];

        $messages = [
            'We want to understand how attendance and fee collection can be managed from one dashboard.',
            'Please share a demo for our principal and accounts team next week.',
            'We are looking for a school management system for student records and exam workflows.',
            'Our school needs a simple platform for teachers, parents, and administrative reporting.',
            'Interested in learning more about admissions, attendance, and billing features.',
            null,
        ];

        DemoRequest::query()->delete();

        for ($i = 1; $i <= 20; $i++) {
            $createdAt = now()->subDays(rand(0, 45))->subMinutes(rand(0, 1440));
            $bookingDate = rand(0, 100) > 28 ? $createdAt->copy()->addDays(rand(1, 14))->toDateString() : null;
            $bookingTime = $bookingDate ? sprintf('%02d:%02d:00', rand(9, 18), rand(0, 1) ? 0 : 30) : null;

            DemoRequest::create([
                'name' => $faker->name(),
                'school_name' => $schools[array_rand($schools)].($i % 5 === 0 ? ' Campus '.$faker->randomElement(['A', 'B', 'North', 'South']) : ''),
                'phone' => '+8801'.$faker->numerify('#########'),
                'student_qty' => rand(0, 100) > 18 ? rand(120, 2800) : null,
                'booking_date' => $bookingDate,
                'booking_time' => $bookingTime,
                'email' => $i % 4 === 0 ? null : $faker->unique()->safeEmail(),
                'message' => $messages[array_rand($messages)],
                'status' => $faker->randomElement([
                    DemoRequestStatus::New,
                    DemoRequestStatus::New,
                    DemoRequestStatus::Contacted,
                    DemoRequestStatus::Scheduled,
                    DemoRequestStatus::Closed,
                ]),
                'created_at' => $createdAt,
                'updated_at' => $createdAt->copy()->addMinutes(rand(5, 480)),
            ]);
        }
    }
}
