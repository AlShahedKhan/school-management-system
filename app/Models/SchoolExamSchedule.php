<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolExamSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'class_name',
        'group_name',
        'section_name',
        'session_name',
        'exam_name',
        'total_subject',
        'submitted_subject',
        'remaining_subject',
        'publish_date',
        'publish_time',
        'status',
        'published_at',
        'created_by',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];
}