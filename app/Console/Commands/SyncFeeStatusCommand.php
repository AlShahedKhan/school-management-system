<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SchoolStudentFee;
use Carbon\Carbon;

class SyncFeeStatusCommand extends Command
{
    protected $signature = 'fees:sync-status';

    protected $description = 'Updates student fee statuses based on due dates (Unpaid -> Due -> Overdue).';

    public function handle()
    {
        $this->info('Starting fee status synchronization...');

        $today = Carbon::today();
        $todayStr = $today->toDateString();

        // 1. Move 'unpaid' to 'due' if due_date has passed
        $unpaidUpdated = SchoolStudentFee::where('status', 'unpaid')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', $todayStr)
            ->update(['status' => 'due']);

        // 2. Move 'partial_paid' to 'due_partial' if due_date has passed
        $partialUpdated = SchoolStudentFee::where('status', 'partial_paid')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', $todayStr)
            ->update(['status' => 'due_partial']);

        // 3. Move 'due' -> 'over_due' when the due_date's month has fully ended
        $dueUpdated = SchoolStudentFee::where('status', 'due')
            ->whereNotNull('due_date')
            ->where(function ($q) use ($today) {
                $q->whereMonth('due_date', '<', $today->month)
                  ->orWhere(function ($q) use ($today) {
                      $q->whereYear('due_date', '<', $today->year);
                  });
            })
            ->update(['status' => 'over_due']);

        $partialDueUpdated = SchoolStudentFee::where('status', 'due_partial')
            ->whereNotNull('due_date')
            ->where(function ($q) use ($today) {
                $q->whereMonth('due_date', '<', $today->month)
                  ->orWhere(function ($q) use ($today) {
                      $q->whereYear('due_date', '<', $today->year);
                  });
            })
            ->update(['status' => 'over_due_partial']);

        $this->info("Completed: {$unpaidUpdated} -> due, {$partialUpdated} -> due_partial, {$dueUpdated} -> over_due, {$partialDueUpdated} -> over_due_partial.");
    }
}
