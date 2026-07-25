<?php

use App\Jobs\PublishScheduledExamResults;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * Schedule the SMS Cleanup Command
 * Runs every day at midnight (Dhaka Time)
 */
Schedule::command('sms:cleanup-expired')
    ->daily()
    ->at('00:00')
    ->timezone('Asia/Dhaka');

// Fee Management Cron Jobs
Schedule::command('fees:monthly-reset')
    ->monthlyOn(1, '00:00')
    ->timezone('Asia/Dhaka')
    ->withoutOverlapping();

Schedule::command('fees:check-daily')
    ->dailyAt('01:00')
    ->timezone('Asia/Dhaka')
    ->withoutOverlapping();

Schedule::command('fees:process-fines')
    ->dailyAt('02:00')
    ->timezone('Asia/Dhaka')
    ->withoutOverlapping();

Schedule::command('fees:sync-status')
    ->dailyAt('01:00')
    ->timezone('Asia/Dhaka')
    ->withoutOverlapping();

Schedule::command('discounts:check-exam-waivers')
    ->dailyAt('03:00')
    ->timezone('Asia/Dhaka')
    ->withoutOverlapping();

Schedule::call(function () {
    PublishScheduledExamResults::dispatch();
})->name('publish-scheduled-exam-results')->everyMinute()->timezone('Asia/Dhaka')->withoutOverlapping();

// Decrement session remaining_days at end of day
Schedule::command('sessions:decrement-remaining-days')
    ->dailyAt('23:59')
    ->timezone('Asia/Dhaka')
    ->withoutOverlapping();

// Dispatch pending SMS campaigns
Schedule::command('sms:send-campaigns')
    ->everyMinute()
    ->timezone('Asia/Dhaka')
    ->withoutOverlapping();