<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SchoolSubscription extends Model
{
    protected $fillable = [
        'school_id',
        'package_id',
        'duration_months',
        'original_price',
        'discount_percent',
        'final_price',
        'start_date',
        'expiry_date',
        'status',
        // New Upgrade Fields
        'upgrade_type',
        'student_limit',
        'teacher_limit',
        'per_student_price',
        'sale_date',
        'contract_close_date'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'start_date' => 'date',
        'expiry_date' => 'date',
        'sale_date' => 'date',
        'contract_close_date' => 'date',
        'final_price' => 'decimal:2',
        'per_student_price' => 'decimal:2',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function isActive()
    {
        return $this->status === 'active' && $this->expiry_date >= Carbon::today();
    }
}
