<?php

namespace App\Models;

use App\Enums\EmployeeStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    protected $fillable = [
        'school_id',
        'employee_no',
        'name',
        'mobile_number',
        'designation',
        'salary_amount',
        'salary_start_date',
        'pay_date',
        'employee_status',
        'note',
    ];

    protected $casts = [
        'salary_amount'     => 'decimal:2',
        'salary_start_date' => 'date',
        'employee_status'   => EmployeeStatusEnum::class,
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function payrolls(): HasMany
    {
        return $this->hasMany(EmployeePayroll::class);
    }

    public function getPaidAmountAttribute(): float
    {
        return (float) $this->payrolls()->sum('receive_amount');
    }

    public function getDueAmountAttribute(): float
    {
        return max(0, (float) $this->salary_amount - $this->paid_amount);
    }

    public function getStatusAttribute(): string
    {
        $paid = $this->paid_amount;
        $salary = (float) $this->salary_amount;
        if ($paid == 0) {
            return 'Due';
        }
        if ($paid < $salary) {
            return 'Partial';
        }
        if ($paid == $salary) {
            return 'Paid';
        }
        return 'Advance';
    }
}