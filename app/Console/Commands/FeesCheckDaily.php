<?php

namespace App\Console\Commands;

use App\Services\FeeStatusSyncService;
use Illuminate\Console\Command;

class FeesCheckDaily extends Command
{
    protected $signature = 'fees:check-daily';
    protected $description = 'Update pending school_student_fees status to due/over_due based on pay_date';

    public function handle(): void
    {
        $updated = app(FeeStatusSyncService::class)->syncPending();
        $this->info("Updated {$updated} fee record(s).");
    }
}
