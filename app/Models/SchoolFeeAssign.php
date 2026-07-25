<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolFeeAssign extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'name',
        'payment_type',
        'is_default',
        'status',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function templates()
    {
        return $this->hasMany(SchoolFeeTemplate::class, 'fee_assign_id');
    }
}
