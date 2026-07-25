<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolFeeTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'class_id',
        'group_id',
        'section_id',
        'session_id',
        'fee_assign_id',
        'fee_type_name',
        'frequency',
        'fee_name',
        'exam_id',
        'pay_date',
        'due_day',
        'amount',
        'description',
        'is_active',
        'food_type',
        'student_ids',
    ];

    protected $casts = [
        'amount'      => 'decimal:2',
        'is_active'   => 'boolean',
        'pay_date'    => 'date:Y-m-d',
        'frequency'   => 'string',
        'due_day'     => 'integer',
        'student_ids' => 'array',
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

    public function assign()
    {
        return $this->belongsTo(SchoolFeeAssign::class, 'fee_assign_id');
    }

    public function schoolExam()
    {
        return $this->belongsTo(SchoolExamName::class, 'exam_id');
    }

    public function studentFees()
    {
        return $this->hasMany(SchoolStudentFee::class, 'fee_template_id');
    }
}
