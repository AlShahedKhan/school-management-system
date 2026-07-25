<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Carbon\Carbon;

use App\Models\AdminSmsPendingQueue;

use App\Models\AdminSmsActivation;

use App\Services\SmsService;

class SendScheduledSmsCampaigns extends Command
{
    protected $signature = 'sms:send-campaigns';

    protected $description = 'Process and send pending event SMS queue within their allowed daily time frames';

    public function handle()
    {
        $now = Carbon::now('Asia/Dhaka');

        $currentTimeStr = $now->format('H:i:s');

        $todayStr = $now->format('Y-m-d');

        $queueItems = AdminSmsPendingQueue::where('is_sent', false)->with('school')->get();

        if ($queueItems->isEmpty()) {
            return;
        }

        $smsService = app(SmsService::class);

        foreach ($queueItems as $item) {
            $school = $item->school;

            if (!$school) {
                $item->delete();

                continue;
            }

            $activation = AdminSmsActivation::where('school_id', $school->id)
                ->where('sms_type', $item->sms_type)
                ->where('is_active', true)
                ->first();

            $shouldProcess = match (true) {
                !$activation => false,

                ($activation->schedule_type->value === 'single' || $activation->schedule_type->value === 'multiple') => in_array($todayStr, $activation->schedule_dates ?: []),

                default => true
            };

            match ($shouldProcess) {
                false => (!$activation ? $item->delete() : null),

                true => match ($currentTimeStr >= $activation->start_time && $currentTimeStr <= $activation->end_time) {
                    true => (function() use ($smsService, $school, $item) {
                        $res = $smsService->sendSms($school, $item->mobile, $item->message, $item->send_channel);

                        match ($res['success']) {
                            true => $item->update(['is_sent' => true]),

                            false => null
                        };
                    })(),

                    false => null
                }
            };
        }
    }
}
