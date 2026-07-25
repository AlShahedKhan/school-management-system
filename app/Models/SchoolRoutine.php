<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolRoutine extends Model
{
    use HasFactory;

    protected $guarded = [];

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

    // Updated to match your Teacher model/table
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }
}
