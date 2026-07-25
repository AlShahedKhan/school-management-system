<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountStudent extends Model
{
    protected $fillable = [
        'discount_id',
        'student_id',
        'before_amount',
        'after_amount',
        'status',
        'cancel_reason',
        'cancelled_at',
    ];

    protected $casts = [
        'before_amount' => 'decimal:2',
        'after_amount'  => 'decimal:2',
        'cancelled_at'  => 'datetime',
    ];

    public function discount()
    {
        return $this->belongsTo(SchoolDiscount::class, 'discount_id');
    }

    public function student()
    {
        return $this->belongsTo(AdmissionStudent::class, 'student_id');
    }

    public function examLogs()
    {
        return $this->hasMany(DiscountExamLog::class, 'discount_student_id');
    }
}
