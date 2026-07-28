<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminSmsCredential extends Model
{
    protected $fillable = [
        'provider_name',
        'api_key',
        'sender_id',
        'api_url',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
