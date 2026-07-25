<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
        'package_type',
        'student_limit',
        'teacher_limit',
        'free_trial_days',
        'per_student_price',
        'total_payable',
        'annual_discount_percent',
        'after_discount',
        'sms_limit',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'student_limit' => 'integer',
            'teacher_limit' => 'integer',
            'free_trial_days' => 'integer',
            'per_student_price' => 'decimal:2',
            'total_payable' => 'decimal:2',
            'annual_discount_percent' => 'decimal:2',
            'after_discount' => 'decimal:2',
            'sms_limit' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByRaw("
            CASE package_type
                WHEN 'Basic' THEN 1
                WHEN 'Standard' THEN 2
                WHEN 'Premium' THEN 3
                WHEN 'Advance' THEN 4
                ELSE 5
            END
        ")->orderBy('id');
    }

    public function subscriptions()
    {
        return $this->hasMany(SchoolSubscription::class);
    }
}
