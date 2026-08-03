<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Single source of truth for a school's available cash balance.
 * Exactly one record per school. All balance mutations flow through
 * App\Services\AccountService — never directly from controllers.
 */
class SystemCashBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'opening_balance',
        'current_balance',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function transactions()
    {
        return $this->hasMany(AccountTransaction::class, 'school_id', 'school_id');
    }
}
