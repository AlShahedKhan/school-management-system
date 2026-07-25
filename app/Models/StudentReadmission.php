<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentReadmission extends Model
{
    use HasFactory;

    protected $table = 'student_readmissions';

    protected $fillable = [
        'school_id',
        'student_id',
        'class_id',
        'from_group_id',
        'to_group_id',
        'from_section_id',
        'to_section_id',
        'from_session_id',
        'to_session_id',
        'student_id_number',
        'from_admission_id',
        'to_admission_id',
        'readmission_date',
        'readmission_fee',
    ];

    /**
     * Relationship: StudentReadmission belongs to a School
     */
    public function school()
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    /**
     * Relationship: StudentReadmission belongs to an AdmissionStudent
     */
    public function student()
    {
        return $this->belongsTo(AdmissionStudent::class, 'student_id');
    }

    /**
     * Relationship: StudentReadmission belongs to a SchoolClass
     */
    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /**
     * Relationship: StudentReadmission belongs to a from SchoolGroup
     */
    public function fromGroup()
    {
        return $this->belongsTo(SchoolGroup::class, 'from_group_id');
    }

    /**
     * Relationship: StudentReadmission belongs to a to SchoolGroup
     */
    public function toGroup()
    {
        return $this->belongsTo(SchoolGroup::class, 'to_group_id');
    }

    /**
     * Relationship: StudentReadmission belongs to a from SchoolSection
     */
    public function fromSection()
    {
        return $this->belongsTo(SchoolSection::class, 'from_section_id');
    }

    /**
     * Relationship: StudentReadmission belongs to a to SchoolSection
     */
    public function toSection()
    {
        return $this->belongsTo(SchoolSection::class, 'to_section_id');
    }

    /**
     * Relationship: StudentReadmission belongs to a from SchoolSession
     */
    public function fromSession()
    {
        return $this->belongsTo(SchoolSession::class, 'from_session_id');
    }

    /**
     * Relationship: StudentReadmission belongs to a to SchoolSession
     */
    public function toSession()
    {
        return $this->belongsTo(SchoolSession::class, 'to_session_id');
    }
}
