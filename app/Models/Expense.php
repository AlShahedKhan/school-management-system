<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'invoice_no',
        'date',
        'expense_reason',
        'details',
        'month',
        'year',
        'name',
        'amount'
    ];
}