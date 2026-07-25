<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id', 'class_id', 'group_id', 'section_id', 'start_date',
        'end_date', 'session_year', 'total_days', 'remaining_days', 'fees_due',
    ];

    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->where('remaining_days', '>', 0)->orWhereNull('remaining_days');
        });
    }

    // Added to allow the Syllabus to find the Session
    public function syllabuses()
    {
        return $this->hasMany(SchoolSyllabus::class, 'session_id');
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function schoolGroup()
    {
        return $this->belongsTo(SchoolGroup::class, 'group_id');
    }

    public function schoolSection()
    {
        return $this->belongsTo(SchoolSection::class, 'section_id');
    }
}
