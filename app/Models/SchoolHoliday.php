<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolHoliday extends Model
{
    protected $fillable = [
        'school_id', 'type', 'class_name', 'group_name', 
        'section_name', 'session', 'reason', 'start_date', 'end_date', 'total_days'
    ];
}