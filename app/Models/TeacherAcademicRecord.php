<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherAcademicRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'teacher_id',
        'session_id',
        'session_year',
        'designation',
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
                $sessionYear = $model->session_year ?? date('Y');

                // Find max serial for this school_id and session_year
                $prefixPattern = "TAR-{$schoolPrefix}-{$sessionYear}-%";
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

                $model->academic_record_id = sprintf("TAR-%s-%s-%06d", $schoolPrefix, $sessionYear, $nextSerial);
            }
        });
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function schoolSession()
    {
        return $this->belongsTo(SchoolSession::class, 'session_id');
    }

    public function isActive()
    {
        return $this->status === 'Active' || strtolower((string)$this->status) === 'active';
    }
}
