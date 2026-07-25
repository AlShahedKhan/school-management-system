<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolAnnouncement extends Model
{
    protected $fillable = [
        'school_id',
        'type',
        'class_name',
        'group_name',
        'section_name',
        'session',
        'title',
        'details',
        'date'
    ];
}
