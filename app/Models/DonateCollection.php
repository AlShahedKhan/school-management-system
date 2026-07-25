<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DonateCollection extends Model
{
    protected $fillable = [
        'school_id',
        'donate_id',
        'paid_amount',
        'receive_month',
        'receive_year',
        'receive_date',
        'note',
    ];

    protected $casts = [
        'paid_amount'  => 'decimal:2',
        'receive_date' => 'date',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function donate(): BelongsTo
    {
        return $this->belongsTo(Donate::class);
    }
}