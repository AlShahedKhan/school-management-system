<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Enums\SmsType;

class AdminSmsTemplate extends Model
{
    protected $fillable = [
        'title',

        'sms_type',

        'template_body',

        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',

        'sms_type' => SmsType::class,
    ];

    public function schools()
    {
        return $this->belongsToMany(School::class, 'admin_sms_template_school');
    }
}
