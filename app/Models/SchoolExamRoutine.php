<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolExamRoutine extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'exam_date',
        'day_name',
        'start_time',
        'end_time',
        'total_hours',
        'class_id',
        'group_id',
        'section_id',
        'session_id',
        'exam_id',
        'subject_id',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

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

    public function schoolExam()
    {
        return $this->belongsTo(SchoolExamName::class, 'exam_id');
    }

    public function schoolSubject()
    {
        return $this->belongsTo(SchoolSubject::class, 'subject_id');
    }
}
