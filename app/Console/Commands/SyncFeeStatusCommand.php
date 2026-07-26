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

        // 2. Move 'partial_paid' to 'partial_due' if due_date has passed
        $partialUpdated = SchoolStudentFee::where('status', 'partial_paid')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', $todayStr)
            ->update(['status' => 'partial_due']);

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

        $partialDueUpdated = SchoolStudentFee::where('status', 'partial_due')
            ->whereNotNull('due_date')
            ->where(function ($q) use ($today) {
                $q->whereMonth('due_date', '<', $today->month)
                  ->orWhere(function ($q) use ($today) {
                      $q->whereYear('due_date', '<', $today->year);
                  });
            })
            ->update(['status' => 'partial_over_due']);

        $this->info("Completed: {$unpaidUpdated} -> due, {$partialUpdated} -> partial_due, {$dueUpdated} -> over_due, {$partialDueUpdated} -> partial_over_due.");
    }
}
