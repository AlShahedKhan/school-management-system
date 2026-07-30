<?php

namespace App\Models;

use App\Enums\TeacherStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Hash;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'name',
        'designation',
        'id_number',
        'mobile',
        'email',
        'password',
        'dob',
        'photo',
        'salary_amount',
        'salary_start_date',
        'pay_date',
        'status',
    ];

    protected $casts = [
        'status' => TeacherStatus::class,
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->id_number)) {
                $model->id_number = generate_school_common_id_number($model->school_id);
            }
        });
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function academicRecords()
    {
        return $this->hasMany(TeacherAcademicRecord::class, 'teacher_id');
    }

    public function latestAcademicRecord()
    {
        return $this->hasOne(TeacherAcademicRecord::class, 'teacher_id')->latestOfMany();
    }

    /**
     * Get all of the teacher's attendances.
     */
    public function attendances(): MorphMany
    {
        return $this->morphMany(Attendance::class, 'attendable');
    }
}
