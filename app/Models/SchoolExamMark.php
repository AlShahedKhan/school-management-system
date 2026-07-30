<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolExamMark extends Model {
    protected $fillable = [
        'school_id', 'class_name', 'group_name', 'section_name', 'session_name',
        'exam_name', 'subject_name', 'student_id_number', 'student_name',
        'roll_no',
        'mark',
        'tutorial_mark',
        'mcq_mark',
        'writing_mark',
        'theory_mark',
        'practical_mark',
        'letter_name',
        'point',
        'status'
    ];
}
