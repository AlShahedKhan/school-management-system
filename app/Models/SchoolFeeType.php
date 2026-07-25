<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolFeeType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'school_id',
        'class_id',
        'group_id',
        'section_id',
        'session_id',
        'fee_type_name',
        'fee_name',      // Added for Admission, Monthly, Others
        'exam_id',       // Added for Exam Fee type
        'student_id',    // Added for Boarding Food type
        'amount',
        'description',
        'pay_date',
    ];

    /**
     * Relationship with the School
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Relationship with Class
     */
    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /**
     * Relationship with Group
     */
    public function schoolGroup()
    {
        return $this->belongsTo(SchoolGroup::class, 'group_id');
    }

    /**
     * Relationship with Section
     */
    public function schoolSection()
    {
        return $this->belongsTo(SchoolSection::class, 'section_id');
    }

    /**
     * Relationship with Session
     */
    public function schoolSession()
    {
        return $this->belongsTo(SchoolSession::class, 'session_id');
    }

    /**
     * Relationship with Exam Name
     */
    public function schoolExam()
    {
        return $this->belongsTo(SchoolExamName::class, 'exam_id');
    }

    /**
     * Relationship with Student (for Boarding Food)
     */
    public function student()
    {
        return $this->belongsTo(AdmissionStudent::class, 'student_id');
    }
}