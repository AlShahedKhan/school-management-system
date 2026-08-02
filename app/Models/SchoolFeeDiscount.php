<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolFeeDiscount extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'class_id',
        'group_id',
        'section_id',
        'session_id',
        'discount_scope',
        'student_id',
        'fee_type_id',
        'fee_name',
        'discount_type',
        'discount_value',
        'minimum_grade',
        'before_discount',
        'discount_amount',
        'after_discount',
    ];

    public function schoolClass() {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function schoolGroup() {
        return $this->belongsTo(SchoolGroup::class, 'group_id');
    }

    public function schoolSection() {
        return $this->belongsTo(SchoolSection::class, 'section_id');
    }

    public function schoolSession() {
        return $this->belongsTo(SchoolSession::class, 'session_id');
    }

    public function student() {
        // Explicitly linking to AdmissionStudent model
        return $this->belongsTo(AdmissionStudent::class, 'student_id');
    }

    public function feeType() {
        return $this->belongsTo(SchoolFeeTemplate::class, 'fee_type_id');
    }
}