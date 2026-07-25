<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolExamName extends Model
{
    protected $fillable = [
        'school_id',
        'class_id',
        'group_id',
        'section_id',
        'session_id',
        'exam_name',
        'exam_start_date',
        'exam_end_date',
    ];

    protected $casts = [
        'exam_start_date' => 'date:Y-m-d',
        'exam_end_date' => 'date:Y-m-d',
    ];

    protected $appends = ['class_name', 'group_name', 'section_name', 'session_name'];

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function schoolGroup()
    {
        return $this->belongsTo(SchoolGroup::class, 'group_id');
    }

    public function schoolSection()
    {
        return $this->belongsTo(SchoolSection::class, 'section_id');
    }

    public function schoolSession()
    {
        return $this->belongsTo(SchoolSession::class, 'session_id');
    }

    public function getClassNameAttribute()
    {
        return $this->schoolClass?->class_name;
    }

    public function getGroupNameAttribute()
    {
        return $this->schoolGroup?->group_name;
    }

    public function getSectionNameAttribute()
    {
        return $this->schoolSection?->section_name;
    }

    public function getSessionNameAttribute()
    {
        return $this->schoolSession?->session_year;
    }
}
