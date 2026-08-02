<?php

namespace App\Repositories;

use App\Models\SystemCashBalance;

/**
 * Repository for the single per-school cash balance record.
 * Row-level locking (lockForUpdate) is applied here so concurrent
 * payments cannot produce race-condition balance updates.
 */
class SystemCashBalanceRepository
{
    /**
     * Fetch the balance row for a school, optionally with a pessimistic
     * row lock for the duration of the surrounding transaction.
     */
    public function forSchool(int $schoolId, bool $forUpdate = false): ?SystemCashBalance
    {
        $query = SystemCashBalance::query()->where('school_id', $schoolId);

        if ($forUpdate) {
            $query->lockForUpdate();
        }

        return $query->first();
    }

    /**
     * Create the balance row for a school when one does not exist yet.
     * Guarantees exactly one record per school.
     */
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

    /**
     * Persist the computed balance. Only called from the AccountService.
     */
    public function setBalance(int $schoolId, float $balance): void
    {
        SystemCashBalance::query()
            ->where('school_id', $schoolId)
            ->update(['current_balance' => $balance]);
    }
}
