<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolExamAdmitCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'class_name',
        'group_name',
        'section_name',
        'session_name',
        'exam_name',
        'student_id_number',
        'admit_card_number'
    ];

    public function student()
    {
        return $this->belongsTo(
            AdmissionStudent::class,
            'student_id_number',
            'student_id_number'
        );
    }
}