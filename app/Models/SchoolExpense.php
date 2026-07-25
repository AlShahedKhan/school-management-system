<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolExpense extends Model
{
    protected $fillable = [
        'school_id',
        'invoice_no',
        'expense_date',
        'expense_reason',
        'amount',
        'balance',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
        'balance' => 'decimal:2',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
