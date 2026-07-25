<?php

namespace App\Jobs;

use App\Models\SchoolExamSchedule;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PublishScheduledExamResults implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public ?int $scheduleId = null)
    {
    }

    public function handle(): void
    {
        $query = SchoolExamSchedule::query()->where('status', 'Active')->whereNull('published_at');

        if ($this->scheduleId) {
            $query->where('id', $this->scheduleId);
        }

        $schedules = $query->get();

        foreach ($schedules as $schedule) {
            $publishAt = \Carbon\Carbon::parse($schedule->publish_date . ' ' . $schedule->publish_time);
            if (now()->lt($publishAt)) {
                continue;
            }

            $schedule->update([
                'status' => 'Published',
                'published_at' => now(),
            ]);
        }
    }
}
