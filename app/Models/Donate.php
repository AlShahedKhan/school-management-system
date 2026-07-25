<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Donate extends Model
{
    protected $fillable = [
        'school_id',
        'donate_no',
        'name',
        'mobile_number',
        'location',
        'donate_reason',
        'amount',
        'note',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function collections(): HasMany
    {
        return $this->hasMany(DonateCollection::class);
    }

    public function getPaidAmountAttribute(): float
    {
        return (float) $this->collections()->sum('paid_amount');
    }

    public function getDueAmountAttribute(): float
    {
        return max(0, (float) $this->amount - $this->paid_amount);
    }

    public function getStatusAttribute(): string
    {
        $paid = $this->paid_amount;
        $amount = (float) $this->amount;
        if ($paid == 0) {
            return 'Due';
        }
        if ($paid < $amount) {
            return 'Partial';
        }
        if ($paid == $amount) {
            return 'Paid';
        }
        return 'Over Due';
    }
}