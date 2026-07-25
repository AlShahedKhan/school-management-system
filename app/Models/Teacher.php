<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

use App\Enums\TeacherStatus; // Added on 2026-07-11: Import status enum

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
        'status', // Added on 2026-07-11
    ];

    // Added on 2026-07-11: Cast status to TeacherStatus Enum
    protected $casts = [
        'status' => TeacherStatus::class,
    ];

    // Added on 2026-07-11: Boot method for auto generating common sequence ID
    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->id_number)) {
                $model->id_number = generate_school_common_id_number($model->school_id);
            }
        });
    }

    // Mutator for password hashing
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    // Relation to School
    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
