<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolSyllabus extends Model
{
    use HasFactory;

    
    protected $table = 'school_syllabuses';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'school_id',
        'session_id',
        'class_id',
        'group_id',
        'section_id',
        'subject_id',
        'exam_id',
        'start_page',
        'end_page'
    ];

    public function school_session()
    {
        return $this->belongsTo(SchoolSession::class, 'session_id');
    }

    public function school_class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function school_group()
    {
        return $this->belongsTo(SchoolGroup::class, 'group_id');
    }

    public function school_section()
    {
        return $this->belongsTo(SchoolSection::class, 'section_id');
    }

    public function school_subject()
    {
        return $this->belongsTo(SchoolSubject::class, 'subject_id');
    }

    public function school_exam()
    {
        return $this->belongsTo(SchoolExamName::class, 'exam_id');
    }
}
