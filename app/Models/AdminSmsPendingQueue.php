<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Enums\SmsType;

use App\Enums\SmsChannel;

class AdminSmsPendingQueue extends Model
{
    protected $table = 'admin_sms_pending_queue';

    protected $fillable = [
        'school_id',

        'mobile',

        'message',

        'send_channel',

        'sms_type',

        'is_sent',
    ];

    protected $casts = [
        'is_sent' => 'boolean',

        'sms_type' => SmsType::class,

        'send_channel' => SmsChannel::class,
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
