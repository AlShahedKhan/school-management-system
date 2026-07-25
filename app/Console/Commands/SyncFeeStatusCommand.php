<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SchoolStudentFee;
use Carbon\Carbon;

class SyncFeeStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fees:sync-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates student fee statuses based on due dates (Unpaid -> Due, Partial Paid -> Partial Due).';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting fee status synchronization...');
        
        $today = Carbon::today()->toDateString();

        // 1. Move 'unpaid' to 'due' if due_date has passed
        $unpaidUpdated = SchoolStudentFee::where('status', 'unpaid')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', $today)
            ->update(['status' => 'due']);

        // 2. Move 'partial_paid' to 'partial_due' if due_date has passed
        $partialUpdated = SchoolStudentFee::where('status', 'partial_paid')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', $today)
            ->update(['status' => 'partial_due']);
            
        // Over Due logic typically triggers on the 1st of the next month.
        // We can check if today is the 1st of the month, and if so, move 'due' to 'over_due'
        if (Carbon::today()->day === 1) {
            $dueUpdated = SchoolStudentFee::where('status', 'due')
                ->whereDate('due_date', '<', $today)
                ->update(['status' => 'over_due']);
                
            $partialDueUpdated = SchoolStudentFee::where('status', 'partial_due')
                ->whereDate('due_date', '<', $today)
                ->update(['status' => 'partial_over_due']);
                
            $this->info("Over Due update: {$dueUpdated} fees marked over_due, {$partialDueUpdated} marked partial_over_due.");
        }

        $this->info("Completed sync: {$unpaidUpdated} moved to due, {$partialUpdated} moved to partial_due.");
    }
}
