<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Mass Assignable Fields
     */
    protected $fillable = [
        'role',            // admin, school, teacher, student
        'name',
        'school_name',
        'email',
        'mobile',
        'id_number',
        'password',
    ];

    /**
     * Hidden Fields
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casts
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * ==========================
     * RELATIONSHIPS
     * ==========================
     */

    // School Owner (One School belongs to one User)
    public function school()
    {
        return $this->hasOne(School::class);
    }

    /**
     * ==========================
     * ROLE HELPERS (Optional but Clean)
     * ==========================
     */

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isSchool()
    {
        return $this->role === 'school';
    }

    public function isTeacher()
    {
        return $this->role === 'teacher';
    }

    public function isStudent()
    {
        return $this->role === 'student';
    }
    public function attendances():MorphMany
    {
        return $this->morphMany(Attendance::class, 'attendable');
    }
}
