<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Principal extends Model
{
    protected $fillable = [
        'school_id',
        'id_number',
        'name',
        'phone',
        'email',
        'pin',
        'photo',
        'signature',
        'designation',
        'joining_date',
        'is_active',
    ];

    protected $hidden = [
        'pin',
    ];

    protected $casts = [
        'joining_date' => 'date:Y-m-d',
        'is_active' => 'boolean',
    ];
}
