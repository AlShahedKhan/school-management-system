<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolDiscount extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'created_by',
        'class_id',
        'group_id',
        'section_id',
        'session_id',
        'discount_category',
        'discount_type',
        'discount_value',
        'fee_template_id',
        'student_scope',
        'auto_apply_new_students',
        'exam_id',
        'min_gpa',
        'min_marks',
        'months',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'discount_value'          => 'decimal:2',
        'min_gpa'                 => 'decimal:2',
        'min_marks'               => 'decimal:2',
        'months'                  => 'array',
        'auto_apply_new_students' => 'boolean',
        'is_active'               => 'boolean',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
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

    public function feeTemplate()
    {
        return $this->belongsTo(SchoolFeeTemplate::class, 'fee_template_id');
    }

    public function exam()
    {
        return $this->belongsTo(SchoolExamName::class, 'exam_id');
    }

    public function discountStudents()
    {
        return $this->hasMany(DiscountStudent::class, 'discount_id');
    }
}
