<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolPayroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id', 'school_employee_id', 'employee_name', 'mobile_number',
        'designation', 'month', 'year', 'present', 'absent', 'leave',
        'total_payable', 'payable_due', 'advance_status', 'pay_type', 'paid_amount'
    ];

    public function employee()
    {
        return $this->belongsTo(SchoolEmployee::class, 'school_employee_id');
    }
}