<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\SchoolHoliday;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SchoolHolidaySeeder extends Seeder
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

        $monthStart = Carbon::now()->startOfMonth();
        $holidays = [
            [
                'type' => 'General',
                'reason' => 'Summer Vacation',
                'start_date' => $monthStart->copy()->addDays(18),
                'end_date' => $monthStart->copy()->addDays(20),
            ],
            [
                'type' => 'General',
                'reason' => 'School Foundation Day',
                'start_date' => $monthStart->copy()->addDays(11),
                'end_date' => $monthStart->copy()->addDays(11),
            ],
            [
                'type' => 'Class Wise',
                'reason' => 'Class Assessment Preparation',
                'class_name' => 'Five',
                'group_name' => null,
                'section_name' => 'A',
                'session' => now()->year,
                'start_date' => $monthStart->copy()->addDays(7),
                'end_date' => $monthStart->copy()->addDays(8),
            ],
            [
                'type' => 'General',
                'reason' => 'Teachers Training Day',
                'start_date' => $monthStart->copy()->subDays(4),
                'end_date' => $monthStart->copy()->subDays(4),
            ],
            [
                'type' => 'Class Wise',
                'reason' => 'Science Fair Preparation',
                'class_name' => 'Eight',
                'group_name' => null,
                'section_name' => 'A',
                'session' => now()->year,
                'start_date' => $monthStart->copy()->subDays(9),
                'end_date' => $monthStart->copy()->subDays(8),
            ],
        ];

        foreach ($holidays as $holiday) {
            $startDate = $holiday['start_date'];
            $endDate = $holiday['end_date'];

            SchoolHoliday::updateOrCreate(
                [
                    'school_id' => $school->id,
                    'reason' => $holiday['reason'],
                    'start_date' => $startDate->toDateString(),
                ],
                [
                    'type' => $holiday['type'],
                    'class_name' => $holiday['class_name'] ?? null,
                    'group_name' => $holiday['group_name'] ?? null,
                    'section_name' => $holiday['section_name'] ?? null,
                    'session' => $holiday['session'] ?? null,
                    'end_date' => $endDate->toDateString(),
                    'total_days' => $startDate->diffInDays($endDate) + 1,
                ]
            );
        }
    }
}
