<?php

namespace App\Repositories;

use App\Models\SystemCashBalance;


class SystemCashBalanceRepository
{

    public function forSchool(int $schoolId, bool $forUpdate = false): ?SystemCashBalance
    {
        $query = SystemCashBalance::query()->where('school_id', $schoolId);

        if ($forUpdate) {
            $query->lockForUpdate();
        }

        return $query->first();
    }


    public function ensure(int $schoolId, float $openingBalance = 0.0): SystemCashBalance
    {
        return SystemCashBalance::firstOrCreate(
            ['school_id' => $schoolId],
            [
                'opening_balance' => $openingBalance,
                'current_balance' => $openingBalance,
            ]
        );
    }


    public function setBalance(int $schoolId, float $balance): void
    {
        SystemCashBalance::query()
            ->where('school_id', $schoolId)
            ->update(['current_balance' => $balance]);
    }
}
