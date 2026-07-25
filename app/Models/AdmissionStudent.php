<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AdmissionStudent extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'student_name',
        'father_name',
        'mother_name',
        'student_id_number',
        'admission_id',
        'roll_no',
        'class',
        'group',
        'section',
        'session',
        'admission_fee',
        'admission_date',
        'mobile',
        'password',
        'image',
        'status',
        'guardian_id',
        'previous_school',
        'previous_class',
        'previous_group',
        'previous_section',
        'previous_session',
        'interview_code',
        'last_exam_result',
        'division',
        'district',
        'upazila',
        'school',
        'current_division',
        'current_district',
        'current_upazila',
        'current_village',
        'current_country',
        'permanent_division',
        'permanent_district',
        'permanent_upazila',
        'permanent_village',
        'permanent_country',
        'inactive_date',
        'inactive_reason',
        'active_date',
        'status_updated_by',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            $schoolUser = User::find($model->school_id);
            $schoolPrefix = $schoolUser?->id_number ? substr($schoolUser->id_number, -5) : '00000';

            // 1. Permanent Student ID
            if (empty($model->student_id_number)) {
                $model->student_id_number = generate_school_common_id_number($model->school_id);
            }

            // 2. Dynamic Admission ID
            $sessionYear = '0000';
            if ($model->session) {
                $sessionYear = SchoolSession::where('id', $model->session)->value('session_year') ?? '0000';
            }

            $lastAdmission = static::where('school_id', $model->school_id)
                ->where('admission_id', 'LIKE', $schoolPrefix . $sessionYear . '%')
                ->orderBy('admission_id', 'desc')
                ->first();

            $nextAdmSerial = $lastAdmission?->admission_id
                ? (int) substr($lastAdmission->admission_id, -4) + 1
                : 1;

            $model->admission_id = $schoolPrefix . $sessionYear . str_pad($nextAdmSerial, 4, '0', STR_PAD_LEFT);

            // 3. Auto Roll Number
            if (empty($model->roll_no) && $model->class && $model->section && $model->session) {
                $maxRoll = (int) static::where('school_id', $model->school_id)
                    ->where('class', $model->class)
                    ->where('section', $model->section)
                    ->where('session', $model->session)
                    ->pluck('roll_no')
                    ->max(fn ($r) => (int) $r);
                $model->roll_no = $maxRoll + 1;
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty(['class', 'section', 'session']) || empty($model->roll_no)) {
                if ($model->class && $model->section && $model->session) {
                    $maxRoll = (int) static::where('school_id', $model->school_id)
                        ->where('class', $model->class)
                        ->where('section', $model->section)
                        ->where('session', $model->session)
                        ->where('id', '!=', $model->id)
                        ->pluck('roll_no')
                        ->max(fn($r) => (int) $r);
                    $model->roll_no = $maxRoll + 1;
                }
            }
        });

        static::created(function ($model) {
            $sessionYear = $model->schoolSession?->session_year
                ?? SchoolSession::where('id', $model->session)->value('session_year')
                ?? date('Y');
            StudentAcademicRecord::firstOrCreate(
                [
                    'school_id'    => $model->school_id,
                    'student_id'   => $model->id,
                    'session_year' => $sessionYear,
                ],
                [
                    'session_id' => $model->session,
                    'class_id'   => $model->class,
                    'group_id'   => $model->group,
                    'section_id' => $model->section,
                    'roll_no'    => $model->roll_no,
                    'status'     => $model->status ?? 'Active',
                ]
            );
        });
    }

    public function guardian()
    {
        return $this->belongsTo(Guardian::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class', 'id');
    }

    public function schoolSection()
    {
        return $this->belongsTo(SchoolSection::class, 'section', 'id');
    }

    public function schoolGroup()
    {
        return $this->belongsTo(SchoolGroup::class, 'group', 'id');
    }

    public function schoolSession()
    {
        return $this->belongsTo(SchoolSession::class, 'session', 'id');
    }

    public function academicRecords()
    {
        return $this->hasMany(StudentAcademicRecord::class, 'student_id');
    }

    public function latestAcademicRecord()
    {
        return $this->hasOne(StudentAcademicRecord::class, 'student_id')->latestOfMany();
    }

    public function isActive()
    {
        return $this->status === 'Active' || strtolower($this->status) === 'active';
    }
}
