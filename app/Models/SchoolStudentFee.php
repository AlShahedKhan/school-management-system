<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolStudentFee extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'student_id',
        'fee_assign_id',
        'fee_template_id',
        'fee_type_name',
        'fee_name',
        'base_amount',
        'discount_amount',
        'payable_amount',
        'paid_amount',
        'due_amount',
        'advance_amount',
        'generation_period',
        'pay_date',
        'due_date',
        'status',
    ];

    protected $casts = [
        'base_amount'     => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'payable_amount'  => 'decimal:2',
        'paid_amount'     => 'decimal:2',
        'due_amount'      => 'decimal:2',
        'advance_amount'  => 'decimal:2',
        'pay_date'        => 'date',
        'due_date'        => 'date',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function student()
    {
        return $this->belongsTo(AdmissionStudent::class, 'student_id');
    }

    public function assign()
    {
        return $this->belongsTo(SchoolFeeAssign::class, 'fee_assign_id');
    }

    public function feeTemplate()
    {
        return $this->belongsTo(SchoolFeeTemplate::class, 'fee_template_id');
    }
}
