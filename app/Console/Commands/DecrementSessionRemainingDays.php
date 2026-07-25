<?php

namespace App\Console\Commands;

use App\Models\SchoolSession;
use Illuminate\Console\Command;

class DecrementSessionRemainingDays extends Command
{
    protected $signature = 'sessions:decrement-remaining-days';
    protected $description = 'Decrement remaining_days by 1 for all active sessions at end of day';

    public function handle(): void
    {
        $updated = SchoolSession::where('remaining_days', '>', 0)
            ->whereNotNull('remaining_days')
            ->decrement('remaining_days', 1);

        $this->info("Decremented remaining_days for {$updated} session(s).");
    }
}
