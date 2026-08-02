<?php

namespace App\Services;

use App\Models\AccountTransaction;
use App\Repositories\AccountTransactionRepository;
use App\Repositories\SystemCashBalanceRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

/**
 * AccountService is the single gateway for all cash movements.
 *
 * Every Cash In and Cash Out runs inside DB::transaction() with a
 * pessimistic lock (lockForUpdate) on the school's system_cash_balances
 * row, guaranteeing serialized, race-free balance updates. Controllers
 * must NEVER update balances or write ledger rows directly.
 */
class AccountService
{
    public const TYPE_CASH_IN  = AccountTransaction::TYPE_CASH_IN;
    public const TYPE_CASH_OUT = AccountTransaction::TYPE_CASH_OUT;

    public const DEFAULT_CASH_IN_MODULE = 'Student Fee Payment';

    /**
     * Map a SchoolPayment fees_type label to the ledger Cash In module.
     */
    public const FEE_TYPE_MODULE_MAP = [
        'admission' => 'Admission Fee',
        'promote'   => 'Promote Fee',
        'exam'      => 'Exam Fee',
        'fine'      => 'Fine Collection',
        'food'      => 'Food Fee',
        'session'   => 'Session Fee',
        'tuition'   => 'Student Fee Payment',
        'due'       => 'Due Collection',
        'advance'   => 'Advance Collection',
    ];

    public static function moduleForFeeType(?string $feesType): string
    {
        if (! $feesType) {
            return self::DEFAULT_CASH_IN_MODULE;
        }

        $needle = strtolower(trim($feesType));

        foreach (self::FEE_TYPE_MODULE_MAP as $keyword => $module) {
            if (str_contains($needle, $keyword)) {
                return $module;
            }
        }

        return self::DEFAULT_CASH_IN_MODULE;
    }

    public function __construct(
        protected SystemCashBalanceRepository $balances,
        protected AccountTransactionRepository $transactions,
    ) {
    }

    /**
     * Ensure the school has exactly one cash balance record. Used during
     * school setup so the row always exists before any financial activity.
     */
    public function ensureBalance(int $schoolId, float $openingBalance = 0.0): void
    {
        $this->balances->ensure($schoolId, $openingBalance);
    }

    /**
     * Record a Cash In (money received) on the school's balance.
     * Returns null when the amount is not positive (no ledger entry).
     */
    public function cashIn(
        int $schoolId,
        float $amount,
        string $sourceModule,
        ?int $referenceId = null,
        array $options = []
    ): ?AccountTransaction {
        return $this->book($schoolId, self::TYPE_CASH_IN, $amount, $sourceModule, $referenceId, $options);
    }

    /**
     * Record a Cash Out (money paid) on the school's balance.
     * Returns null when the amount is not positive (no ledger entry).
     */
    public function cashOut(
        int $schoolId,
        float $amount,
        string $sourceModule,
        ?int $referenceId = null,
        array $options = []
    ): ?AccountTransaction {
        return $this->book($schoolId, self::TYPE_CASH_OUT, $amount, $sourceModule, $referenceId, $options);
    }

    /**
     * Current available balance of the school.
     */
    public function currentBalance(int $schoolId): float
    {
        $balance = $this->balances->forSchool($schoolId);

        return $balance ? (float) $balance->current_balance : 0.0;
    }

    /**
     * Ledger-derived report summary for Finance Report, Trial Balance and
     * Profit & Loss. All figures come from system_cash_balances and
     * account_transactions — the single source of truth.
     */
    public function reportSummary(int $schoolId, ?string $fromDate = null, ?string $toDate = null): array
    {
        $balance = $this->balances->forSchool($schoolId);

        $transactions = AccountTransaction::query()
            ->where('school_id', $schoolId)
            ->when($fromDate, fn ($q) => $q->whereDate('created_at', '>=', $fromDate))
            ->when($toDate, fn ($q) => $q->whereDate('created_at', '<=', $toDate));

        $cashIn  = (float) (clone $transactions)->where('transaction_type', self::TYPE_CASH_IN)->sum('amount');
        $cashOut = (float) (clone $transactions)->where('transaction_type', self::TYPE_CASH_OUT)->sum('amount');

        $cashInByModule = (clone $transactions)
            ->where('transaction_type', self::TYPE_CASH_IN)
            ->selectRaw('source_module, COUNT(*) as entries, SUM(amount) as total')
            ->groupBy('source_module')
            ->orderByDesc('total')
            ->get();

        $cashOutByModule = (clone $transactions)
            ->where('transaction_type', self::TYPE_CASH_OUT)
            ->selectRaw('source_module, COUNT(*) as entries, SUM(amount) as total')
            ->groupBy('source_module')
            ->orderByDesc('total')
            ->get();

        return [
            'opening_balance'   => $balance ? (float) $balance->opening_balance : 0.0,
            'current_balance'   => $balance ? (float) $balance->current_balance : 0.0,
            'total_cash_in'     => $cashIn,
            'total_cash_out'    => $cashOut,
            'net'               => $cashIn - $cashOut,
            'cash_in_by_module' => $cashInByModule,
            'cash_out_by_module' => $cashOutByModule,
        ];
    }

    /**
     * Paginated immutable ledger history for a school.
     */
    public function ledgerHistory(int $schoolId, array $filters = [], int $perPage = 50)
    {
        return $this->transactions->history($schoolId, $filters, $perPage);
    }

    /**
     * Trial Balance derived from the ledger. For the single per-school cash
     * account, the debit side (opening balance + cash in) must equal the
     * credit side (cash out + current balance). `balanced` flags any
     * inconsistency in the immutable ledger.
     */
    public function trialBalance(int $schoolId): array
    {
        $summary = $this->reportSummary($schoolId);

        $debits  = $summary['opening_balance'] + $summary['total_cash_in'];
        $credits = $summary['total_cash_out'] + $summary['current_balance'];

        return [
            'opening_balance' => $summary['opening_balance'],
            'current_balance' => $summary['current_balance'],
            'total_cash_in'   => $summary['total_cash_in'],
            'total_cash_out'  => $summary['total_cash_out'],
            'debit_total'     => $debits,
            'credit_total'    => $credits,
            'difference'      => $debits - $credits,
            'balanced'        => abs($debits - $credits) < 0.01,
        ];
    }

    /**
     * Low-level immutable ledger append. Exposed for Reverse / Adjustment
     * entries and future double-entry integration. Use the dedicated
     * cashIn/cashOut methods for ordinary receipts and payments.
     */
    public function createTransaction(array $data): AccountTransaction
    {
        return $this->transactions->create($data);
    }

    /**
     * Core balance operation: lock the school's balance row, compute the
     * before/after balances, persist the balance and append the ledger
     * entry inside a single database transaction.
     */
    protected function book(
        int $schoolId,
        string $type,
        float $amount,
        string $sourceModule,
        ?int $referenceId,
        array $options
    ): ?AccountTransaction {
        if ($amount <= 0) {
            return null;
        }

        return DB::transaction(function () use ($schoolId, $type, $amount, $sourceModule, $referenceId, $options) {
            // First activity for this school: create the balance row lazily
            // (normally created during school setup).
            $balance = $this->balances->forSchool($schoolId, true);
            if (! $balance) {
                $this->balances->ensure($schoolId, 0.0);
                $balance = $this->balances->forSchool($schoolId, true);
            }

            $before = (float) $balance->current_balance;
            $after  = $type === self::TYPE_CASH_IN ? $before + $amount : $before - $amount;

            if ($after < 0 && ! Arr::get($options, 'allow_negative', false)) {
                throw new \DomainException('Insufficient cash balance to complete the transaction.');
            }

            $this->balances->setBalance($schoolId, $after);

            return $this->transactions->create([
                'school_id'        => $schoolId,
                'transaction_type' => $type,
                'source_module'    => $sourceModule,
                'reference_id'     => $referenceId,
                'amount'           => $amount,
                'balance_before'   => $before,
                'balance_after'    => $after,
                'before_discount'  => Arr::get($options, 'before_discount'),
                'after_discount'   => Arr::get($options, 'after_discount'),
                'remarks'          => Arr::get($options, 'remarks'),
                'ip_address'       => Arr::get($options, 'ip_address', request()->ip()),
                'created_by'       => Arr::get($options, 'created_by', auth()->id()),
                'created_at'       => now(),
            ]);
        });
    }
}
