<?php

namespace App\Repositories;

use App\Models\AccountTransaction;
use Illuminate\Support\Arr;


class AccountTransactionRepository
{

    public function create(array $data): AccountTransaction
    {
        return AccountTransaction::create(Arr::only($data, [
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
        ]));
    }


    public function history(int $schoolId, array $filters = [], int $perPage = 50)
    {
        $query = AccountTransaction::query()
            ->with('creator')
            ->where('school_id', $schoolId);

        if ($from = Arr::get($filters, 'from_date')) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = Arr::get($filters, 'to_date')) {
            $query->whereDate('created_at', '<=', $to);
        }
        if ($type = Arr::get($filters, 'transaction_type')) {
            $query->where('transaction_type', $type);
        }
        if ($module = Arr::get($filters, 'source_module')) {
            $query->where('source_module', $module);
        }

        return $query->latest('id')->paginate($perPage);
    }
}
