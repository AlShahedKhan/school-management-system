<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentPromotion extends Model
{
    use HasFactory;

    protected $table = 'student_promotions';

    protected $fillable = [
        'school_id',
        'student_id',
        'from_class_id',
        'from_group_id',
        'from_section_id',
        'from_session_id',
        'from_student_id_number',
        'to_class_id',
        'to_group_id',
        'to_section_id',
        'to_session_id',
        'to_student_id_number',
        'from_admission_id',
        'to_admission_id',
        'promote_date',
        'promote_fee',
    ];

    /**
     * Relationship: StudentPromotion belongs to a School
     */
    public function school()
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    /**
     * Relationship: StudentPromotion belongs to an AdmissionStudent
     */
    public function student()
    {
        return $this->belongsTo(AdmissionStudent::class, 'student_id');
    }

    /**
     * Relationship: StudentPromotion belongs to a from SchoolClass
     */
    public function fromClass()
    {
        return $this->belongsTo(SchoolClass::class, 'from_class_id');
    }

    /**
     * Relationship: StudentPromotion belongs to a to SchoolClass
     */
    public function toClass()
    {
        return $this->belongsTo(SchoolClass::class, 'to_class_id');
    }

    /**
     * Relationship: StudentPromotion belongs to a from SchoolGroup
     */
    public function fromGroup()
    {
        return $this->belongsTo(SchoolGroup::class, 'from_group_id');
    }

    /**
     * Relationship: StudentPromotion belongs to a to SchoolGroup
     */
    public function toGroup()
    {
        return $this->belongsTo(SchoolGroup::class, 'to_group_id');
    }

    /**
     * Relationship: StudentPromotion belongs to a from SchoolSection
     */
    public function fromSection()
    {
        return $this->belongsTo(SchoolSection::class, 'from_section_id');
    }

    /**
     * Relationship: StudentPromotion belongs to a to SchoolSection
     */
    public function toSection()
    {
        return $this->belongsTo(SchoolSection::class, 'to_section_id');
    }

    /**
     * Relationship: StudentPromotion belongs to a from SchoolSession
     */
    public function fromSession()
    {
        return $this->belongsTo(SchoolSession::class, 'from_session_id');
    }

    /**
     * Relationship: StudentPromotion belongs to a to SchoolSession
     */
    public function toSession()
    {
        return $this->belongsTo(SchoolSession::class, 'to_session_id');
    }
}
