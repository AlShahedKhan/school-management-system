<?php

namespace Database\Seeders;

use App\Models\DashboardNews;
use Illuminate\Database\Seeder;

class DashboardNewsSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'label' => 'News',
                'message' => '15th — all students must complete registration before the deadline.',
                'sort_order' => 1,
            ],
            [
                'label' => 'Notice',
                'message' => 'Exam routine updates are now available for all active classes.',
                'sort_order' => 2,
            ],
            [
                'label' => 'Update',
                'message' => 'Fee collection reports have been refreshed for the current session.',
                'sort_order' => 3,
            ],
            [
                'label' => 'Alert',
                'message' => 'Please review pending attendance entries before the end of the day.',
                'sort_order' => 4,
            ],
            [
                'label' => 'Info',
                'message' => 'Holiday and leave requests can now be checked from the notification menu.',
                'sort_order' => 5,
            ],
        ];

        foreach ($items as $item) {
            DashboardNews::updateOrCreate(
                ['message' => $item['message']],
                [
                    'label' => $item['label'],
                    'starts_at' => null,
                    'ends_at' => null,
                    'sort_order' => $item['sort_order'],
                    'is_active' => true,
                ]
            );
        }
    }
}
