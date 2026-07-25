<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountExamLog extends Model
{
    protected $fillable = [
        'discount_student_id',
        'exam_id',
        'exam_gpa',
        'exam_marks',
        'passed',
        'action_taken',
    ];

    protected $casts = [
        'exam_gpa'   => 'decimal:2',
        'exam_marks' => 'decimal:2',
        'passed'     => 'boolean',
    ];

    public function discountStudent()
    {
        return $this->belongsTo(DiscountStudent::class, 'discount_student_id');
    }

    public function exam()
    {
        return $this->belongsTo(SchoolExamName::class, 'exam_id');
    }
}
