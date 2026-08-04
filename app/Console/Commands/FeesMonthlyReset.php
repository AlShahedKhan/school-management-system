<?php

namespace App\Console\Commands;

use App\Jobs\GenerateMonthlyFeesForTemplate;
use App\Models\SchoolFeeTemplate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class FeesMonthlyReset extends Command
{
    protected $signature = 'fees:monthly-reset';
    protected $description = 'Generate monthly fee records for active monthly templates due today';

    public function handle(): void
    {
        $todayDay = now()->day;

        $templates = SchoolFeeTemplate::whereIn('fee_type_name', ['Tuition', 'Food'])
            ->where('is_active', true)
            ->where('due_day', $todayDay)
            ->get(['id']);

        if ($templates->isEmpty()) {
            $this->info('No active monthly templates found.');
            return;
        }

        $batch = Bus::batch([])->name('Monthly Fee Reset')->dispatch();

        $templates->chunk(50)->each(function ($chunk) use ($batch) {
            foreach ($chunk as $template) {
                $batch->add(new GenerateMonthlyFeesForTemplate($template->id));
            }
        });

        $this->info("Dispatched {$templates->count()} monthly fee generation jobs.");
    }
}
