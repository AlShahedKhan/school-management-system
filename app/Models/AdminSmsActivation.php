<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Enums\SmsType;

use App\Enums\SmsChannel;

class AdminSmsActivation extends Model
{
    protected $fillable = [
        'school_id',

        'sms_type',

        'admin_sms_template_id',

        'start_time',

        'end_time',

        'schedule_type',

        'schedule_dates',

        'send_channel',

        'is_active',
    ];

    protected $casts = [
        'schedule_dates' => 'array',

        'is_active' => 'boolean',

        'sms_type' => SmsType::class,

        'send_channel' => SmsChannel::class,
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function template()
    {
        return $this->belongsTo(AdminSmsTemplate::class, 'admin_sms_template_id');
    }
}
