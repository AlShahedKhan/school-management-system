<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvancePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'student_id',
        'amount',
        'monthly_fee',
        'full_months',
        'partial_credit',
        'remaining_credit',
        'pay_date',
        'pay_method',
        'notes',
    ];

    protected $casts = [
        'amount'           => 'decimal:2',
        'monthly_fee'      => 'decimal:2',
        'partial_credit'   => 'decimal:2',
        'remaining_credit' => 'decimal:2',
        'pay_date'         => 'date:Y-m-d',
    ];

    public function student()
    {
        return $this->belongsTo(AdmissionStudent::class, 'student_id');
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
