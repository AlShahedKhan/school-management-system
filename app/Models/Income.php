<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    protected $fillable = [
        'school_id',
        'member_no',
        'date',
        'income_source',
        'name',
        'mobile',
        'amount',
        'address',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
