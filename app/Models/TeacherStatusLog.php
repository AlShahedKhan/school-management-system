<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherStatusLog extends Model
{
    // Added on 2026-07-11: TeacherStatusLog fillable fields
    protected $fillable = [
        'school_id',
        'teacher_id',
        'status',
        'action',
        'changed_by',
        'replacement_teacher_id',
        'notes',
    ];
}
