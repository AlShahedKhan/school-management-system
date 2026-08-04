<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'school_student_fee_id',
        'admission_student_id',
        'fees_type',
        'fee_name',
        'status',
        'total_payable',
        'payable_due',
        'total_amount',
        'total_due',
        'pay_type',
        'type_amount',
        'pay_date',
        'for_month',
        'pay_method'
    ];

    public function student()
    {
        return $this->belongsTo(AdmissionStudent::class, 'admission_student_id');
    }

    public function schoolStudentFee()
    {
        return $this->belongsTo(SchoolStudentFee::class, 'school_student_fee_id');
    }
}
