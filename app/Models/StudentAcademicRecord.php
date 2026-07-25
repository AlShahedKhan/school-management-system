<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\AcademicRecordStatus;

class StudentAcademicRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'student_id',
        'session_id',
        'session_year',
        'class_id',
        'group_id',
        'section_id',
        'roll_no',
        'academic_record_id',
        'status',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->academic_record_id)) {
                $schoolUser = User::find($model->school_id);
                $rawDigits = preg_replace('/[^0-9]/', '', $schoolUser?->id_number ?? (string)$model->school_id);
                $schoolPrefix = str_pad(substr($rawDigits, -5), 5, '0', STR_PAD_LEFT);
                $sessionYear = $model->session_year ?? '0000';

                // Find max serial for this school_id and session_year
                $prefixPattern = "AR-{$schoolPrefix}-{$sessionYear}-%";
                $lastRecord = static::where('school_id', $model->school_id)
                    ->where('academic_record_id', 'LIKE', $prefixPattern)
                    ->orderBy('academic_record_id', 'desc')
                    ->first();

                $nextSerial = 1;
                if ($lastRecord && $lastRecord->academic_record_id) {
                    $parts = explode('-', $lastRecord->academic_record_id);
                    $lastNum = (int) end($parts);
                    $nextSerial = $lastNum + 1;
                }

                $model->academic_record_id = sprintf("AR-%s-%s-%06d", $schoolPrefix, $sessionYear, $nextSerial);
            }
        });
    }

    public function student()
    {
        return $this->belongsTo(AdmissionStudent::class, 'student_id');
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function schoolSection()
    {
        return $this->belongsTo(SchoolSection::class, 'section_id');
    }

    public function schoolGroup()
    {
        return $this->belongsTo(SchoolGroup::class, 'group_id');
    }

    public function schoolSession()
    {
        return $this->belongsTo(SchoolSession::class, 'session_id');
    }

    public function isActive()
    {
        return $this->status === 'Active' || strtolower($this->status) === 'active';
    }


    //used for security check
    public function canPerformAcademicOperation(): array
    {
        $student = $this->student;
        $session = $this->schoolSession;

        if (!$student || !$student->isActive()) {
            return [
                'status' => false,
                'message' => 'Student status is not active. Academic operations are blocked.'
            ];
        }

        if (!$this->isActive()) {
            return [
                'status' => false,
                'message' => 'Student academic record status is not active.'
            ];
        }

        if (!$session) {
            return [
                'status' => false,
                'message' => 'Associated academic session not found.'
            ];
        }

        $now = \Carbon\Carbon::now();
        $endDate = $session->end_date ? \Carbon\Carbon::parse($session->end_date)->endOfDay() : null;

        if ($endDate && $now->gt($endDate)) {
            return [
                'status' => false,
                'message' => "Academic session is locked. Ended on {$session->end_date}."
            ];
        }

        if ($session->remaining_days !== null && $session->remaining_days <= 0) {
            return [
                'status' => false,
                'message' => 'Academic session is locked because remaining days is zero.'
            ];
        }

        if ($this->school_id !== $student->school_id) {
            return [
                'status' => false,
                'message' => 'Student school mismatch.'
            ];
        }

        if ($this->session_year !== $session->session_year) {
            return [
                'status' => false,
                'message' => 'Academic record session year mismatch.'
            ];
        }

        return [
            'status' => true,
            'message' => 'Security checks passed.'
        ];
    }
}
