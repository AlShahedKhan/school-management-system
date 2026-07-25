<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SmsPackagePurchase;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CleanupExpiredSms extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'sms:cleanup-expired';

    /**
     * The console command description.
     */
    protected $description = 'Deduct expired SMS credits from schools master balance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now('Asia/Dhaka');

        // 1. Find all approved packages that have expired and still have available SMS
        $expiredPurchases = SmsPackagePurchase::where('status', 'approved')
            ->where('expiry_date', '<', $now)
            ->where('available_sms', '>', 0)
            ->get();

        if ($expiredPurchases->isEmpty()) {
            $this->info('No expired packages found.');
            return;
        }

        $this->info("Found {$expiredPurchases->count()} expired records. Processing...");

        foreach ($expiredPurchases as $purchase) {
            DB::transaction(function () use ($purchase) {
                $remaining = $purchase->available_sms;

                // 2. Deduct only the remaining amount from the school table
                DB::table('schools')
                    ->where('id', $purchase->school_id)
                    ->decrement('sms_balance', $remaining);

                // 3. Mark this record as fully used/expired so it's not processed again
                // We set available to 0 because they can no longer use them.
                $purchase->update([
                    'available_sms' => 0,
                    'admin_note' => $purchase->admin_note . " | System: Automatically expired on " . now()->format('Y-m-d')
                ]);
            });
        }

        $this->info('Cleanup completed successfully.');
    }
}