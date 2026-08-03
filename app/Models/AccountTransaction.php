<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Permanent financial ledger entry. Immutable — records are never updated
 * or deleted. Corrections are recorded as Reverse / Adjustment entries.
 */
class AccountTransaction extends Model
{
    use HasFactory;

    public const TYPE_CASH_IN  = 'Cash In';
    public const TYPE_CASH_OUT = 'Cash Out';

    /**
     * Ledger rows only ever gain a created_at timestamp; the updated_at
     * column does not exist on this table, keeping the audit trail immutable.
     */
    public const UPDATED_AT = null;

    protected $fillable = [
        'school_id',
        'transaction_type',
        'source_module',
        'reference_id',
        'amount',
        'balance_before',
        'balance_after',
        'before_discount',
        'after_discount',
        'remarks',
        'ip_address',
        'created_by',
        'created_at',
    ];

    protected $casts = [
        'amount'          => 'decimal:2',
        'balance_before'  => 'decimal:2',
        'balance_after'   => 'decimal:2',
        'before_discount' => 'decimal:2',
        'after_discount'  => 'decimal:2',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function balance()
    {
        return $this->belongsTo(SystemCashBalance::class, 'school_id', 'school_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
