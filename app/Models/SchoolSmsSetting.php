<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolSmsSetting extends Model
{
    protected $fillable = [
        'school_id',
        'sms_type',
        'template_body',
        'status',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
