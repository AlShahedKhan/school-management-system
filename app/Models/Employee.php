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

    /**
     * Get dynamic status for a target month and year based on pay_date, dates, and payments.
     */
    public function getDynamicStatusForMonth($targetMonth = null, $targetYear = null): string
    {
        $targetMonth = $targetMonth ? (int) $targetMonth : (int) now()->format('m');
        $targetYear  = $targetYear  ? (int) $targetYear  : (int) now()->format('Y');

        $monthlySalary = (float) $this->salary_amount;

        $totalPaid = (float) $this->payrolls()
            ->where('receive_month', $targetMonth)
            ->where('receive_year', $targetYear)
            ->sum('receive_amount');

        if ($totalPaid >= $monthlySalary && $monthlySalary > 0) {
            return 'Paid';
        }

        if ($totalPaid > 0 && $totalPaid < $monthlySalary) {
            return 'Partial Paid';
        }

        $currentDay   = (int) now()->format('d');
        $currentMonth = (int) now()->format('m');
        $currentYear  = (int) now()->format('Y');
        $payDay       = (int) ($this->pay_date ?? 10);

        if ($targetMonth == $currentMonth && $targetYear == $currentYear) {
            if ($currentDay <= $payDay) {
                return 'Running';
            }
            return 'Due';
        }

        if ($targetYear < $currentYear || ($targetYear == $currentYear && $targetMonth < $currentMonth)) {
            return 'Over Due';
        }

        return 'Unpaid';
    }

    public function getPaidAmountAttribute(): float
    {
        return (float) $this->payrolls()->sum('receive_amount');
    }

    public function getDueAmountAttribute(): float
    {
        $startDateStr = $this->salary_start_date;
        if (!$startDateStr) {
            return 0.0;
        }

        $startDate = \Carbon\Carbon::parse($startDateStr)->startOfMonth();
        $currentDate = \Carbon\Carbon::now()->startOfMonth();

        if ($startDate->isAfter($currentDate)) {
            return 0.0;
        }

        $monthsDifference = $startDate->diffInMonths($currentDate) + 1;
        $totalPayable = $monthsDifference * (float) $this->salary_amount;

        return (float) max(0.0, $totalPayable - $this->paid_amount);
    }

    public function getStatusAttribute(): string
    {
        $startDateStr = $this->salary_start_date;
        if (!$startDateStr) {
            return 'Unpaid';
        }

        $startDate = \Carbon\Carbon::parse($startDateStr)->startOfMonth();
        $currentDate = \Carbon\Carbon::now()->startOfMonth();

        if ($startDate->isAfter($currentDate)) {
            return 'Unpaid';
        }

        $monthsDifference = $startDate->diffInMonths($currentDate) + 1;
        $totalPayable = $monthsDifference * (float) $this->salary_amount;
        $totalPaid = $this->paid_amount;

        if ($totalPayable <= 0) {
            return 'Unpaid';
        }

        if ($totalPaid >= $totalPayable) {
            return 'Paid';
        }

        $currentDay = (int) now()->format('d');
        $payDay = 10;
        if ($this->pay_date && preg_match('/(\d+)/', $this->pay_date, $matches)) {
            $payDay = (int) $matches[1];
        }

        if ($totalPaid == 0) {
            if ($currentDay <= $payDay && $monthsDifference == 1) {
                return 'Unpaid';
            }
        }

        $previousPayable = ($monthsDifference > 1) ? ($monthsDifference - 1) * (float) $this->salary_amount : 0.0;

        if ($totalPaid >= $previousPayable) {
            if ($currentDay > $payDay) {
                return 'Due';
            } else {
                return 'Partial Paid';
            }
        }

        return 'Over Due';
    }
}