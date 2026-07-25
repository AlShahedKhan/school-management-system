<?php

namespace App\Models;

use App\Enums\PaymentMethodEnum;
use App\Enums\SalaryTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeePayroll extends Model
{
    protected $fillable = [
        'school_id',
        'employee_id',
        'salary_type',
        'receive_amount',
        'receive_month',
        'receive_year',
        'receive_date',
        'payment_method',
        'bank_name',
        'transaction_id',
        'note',
    ];

    protected $casts = [
        'salary_type'    => SalaryTypeEnum::class,
        'payment_method' => PaymentMethodEnum::class,
        'receive_amount' => 'decimal:2',
        'receive_date'   => 'date',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}