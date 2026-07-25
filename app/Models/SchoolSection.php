<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id', 'class_id', 'group_id', 'section_name',
    ];

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function schoolGroup()
    {
        return $this->belongsTo(SchoolGroup::class, 'group_id');
    }
}
