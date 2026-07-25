<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolEmployee extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id', 'employee_name', 'mobile_number', 'designation',
        'monthly_leave', 'salary_amount', 'payroll_date', 'bank_name',
        'branch', 'routing_number', 'ac_holder_name', 'ac_number'
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}