<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolExamGrade extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'grade_name',
        'grade_point',
        'full_mark',
        'mark_from',
        'mark_to',
        'note'
    ];
}
