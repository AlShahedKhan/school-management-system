<?php

namespace App\Services;

use App\Models\SmsPackagePurchase;

use App\Models\School;

use Illuminate\Support\Facades\Http;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Log;

use Carbon\Carbon;

use App\Models\AdminSmsActivation;

use App\Models\AdminSmsTemplate;

use App\Models\AdminSmsPendingQueue;

use App\Enums\SmsType;

use App\Enums\SmsChannel;

class SmsService
{
    public function sendSms($school, $mobile, $message, $channel = 'message')
    {
        try {
            $now = Carbon::now('Asia/Dhaka');

            $charCount = mb_strlen($message, 'UTF-8');

            $smsCost = (int) ceil($charCount / 70);

            $activePurchase = SmsPackagePurchase::where('school_id', $school->id)
                ->where('status', 'approved')
                ->where('expiry_date', '>=', $now)
                ->where('available_sms', '>=', $smsCost)
                ->orderBy('expiry_date', 'asc')
                ->first();

            $currentSchoolBalance = match ($activePurchase ? true : false) {
                true => null,
                false => School::where('id', $school->id)->value('sms_balance')
            };

            $insufficient = match ($activePurchase ? true : false) {
                true => false,
                false => $currentSchoolBalance < $smsCost
            };

            if ($insufficient) {
                return [
                    'success' => false,
                    'message' => 'SMS balance insufficient. Current Balance: ' . ($currentSchoolBalance ?? 0)
                ];
            }

            $phone = preg_replace('/[^0-9]/', '', $mobile);

            if (str_starts_with($phone, '0')) {
                $phone = '880' . substr($phone, 1);
            }

            $baseUrl = rtrim(env('SMS_BASE_URL'), '/');

            $response = Http::withoutVerifying()
                ->withHeaders([
                    'Accept'       => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->timeout(15)
                ->post("{$baseUrl}/api/SmsSending/SMS", [
                    "UserName"        => (string) env('SMS_USERNAME'),
                    "Apikey"          => (string) env('SMS_API_KEY'),
                    "MobileNumber"    => (string) $phone,
                    "CampaignId"      => "null",
                    "SenderName"      => (string) env('SMS_SENDER_ID'),
                    "TransactionType" => "T",
                    "Message"         => (string) $message
                ]);

            $result = $response->json();

            $isSuccess = ($response->successful() && isset($result['statusCode']) && $result['statusCode'] == 200);

            return match ($isSuccess) {
                true => (function() use ($school, $activePurchase, $smsCost, $result) {
                    DB::transaction(function () use ($school, $activePurchase, $smsCost) {
                        if ($activePurchase) {
                            $activePurchase->decrement('available_sms', $smsCost);
                            $activePurchase->increment('used_sms', $smsCost);
                        }
                        School::where('id', $school->id)->decrement('sms_balance', $smsCost);
                    });
                    return [
                        'success' => true,
                        'trxnId'  => $result['trxnId'] ?? null,
                        'cost'    => $smsCost
                    ];
                })(),
                false => [
                    'success' => false,
                    'message' => $result['responseResult'] ?? $result['message'] ?? 'Gateway Rejection'
                ]
            };
        } catch (\Exception $e) {
            Log::error("SmsService Error: " . $e->getMessage());

            return [
                'success' => false,
                'message' => 'SMS Gateway Connection Failed'
            ];
        }
    }

    public function triggerEventSms($school, $mobile, $type, array $placeholders, $defaultTemplateBody)
    {
        try {
            $now = Carbon::now('Asia/Dhaka');

            $resolvedType = $type instanceof SmsType ? $type : SmsType::tryFrom($type);

            $activation = AdminSmsActivation::where('school_id', $school->id)
                ->where('sms_type', $resolvedType)
                ->where('is_active', true)
                ->first();

            if (!$activation) {
                return [
                    'success' => false,
                    'message' => 'SMS Activation not enabled for this event/school.'
                ];
            }

            $todayStr = $now->format('Y-m-d');

            $scheduleAllowed = match ($activation->schedule_type) {
                'single', 'multiple' => in_array($todayStr, $activation->schedule_dates ?: []),
                default => true
            };

            if (!$scheduleAllowed) {
                return [
                    'success' => false,
                    'message' => 'SMS sending not scheduled for today.'
                ];
            }

            $templateBody = match (true) {
                !empty($activation->admin_sms_template_id) => (function() use ($activation) {
                    $customTemplate = AdminSmsTemplate::find($activation->admin_sms_template_id);
                    return $customTemplate ? $customTemplate->template_body : null;
                })(),
                default => AdminSmsTemplate::where('sms_type', $resolvedType)
                    ->where('is_default', true)
                    ->value('template_body')
            } ?? $defaultTemplateBody;

            foreach ($placeholders as $placeholder => $value) {
                $templateBody = str_replace($placeholder, $value ?? '', $templateBody);
            }

            $currentTimeStr = $now->format('H:i:s');

            $startTime = $activation->start_time;

            $endTime = $activation->end_time;

            $inWindow = ($currentTimeStr >= $startTime && $currentTimeStr <= $endTime);

            return match ($inWindow) {
                true => $this->sendSms($school, $mobile, $templateBody, $activation->send_channel),
                false => (function() use ($school, $mobile, $templateBody, $activation, $resolvedType) {
                    AdminSmsPendingQueue::create([
                        'school_id' => $school->id,
                        'mobile' => $mobile,
                        'message' => $templateBody,
                        'send_channel' => $activation->send_channel,
                        'sms_type' => $resolvedType,
                        'is_sent' => false
                    ]);
                    return [
                        'success' => true,
                        'message' => 'SMS queued for allowed hours.'
                    ];
                })()
            };
        } catch (\Exception $e) {
            Log::error("SmsService triggerEventSms Error: " . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Failed to trigger event SMS: ' . $e->getMessage()
            ];
        }
    }
}
