<?php

namespace App\Services;

use App\Models\SchoolPayment;
use App\Models\SchoolStudentFee;
use Carbon\Carbon;

class FeeStatusSyncService
{
    public function syncPending(?int $schoolId = null): int
    {
        $query = SchoolStudentFee::whereIn('status', ['unpaid', 'pending'])
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', Carbon::today());

        if ($schoolId) {
            $query->where('school_id', $schoolId);
        }

        $updated = 0;

        $query->chunk(100, function ($fees) use (&$updated) {
            foreach ($fees as $fee) {
                $newStatus = $this->computeStatus($fee);
                if ($fee->status !== $newStatus) {
                    $fee->update(['status' => $newStatus]);
                    $updated++;
                }
            }
        });

        return $updated;
    }

    public function syncSingle(SchoolStudentFee $fee): void
    {
        $newStatus = $this->computeStatus($fee);
        if ($fee->status !== $newStatus) {
            $fee->update(['status' => $newStatus]);
        }
    }

    private function computeStatus(SchoolStudentFee $fee): string
    {
        $paid = (float) SchoolPayment::where('school_student_fee_id', $fee->id)
            ->sum('type_amount');
        $amount = (float) ($fee->payable_amount ?: $fee->amount);
        $today = Carbon::today();
        $dueDate = $fee->due_date ? Carbon::parse($fee->due_date) : null;

        return match (true) {
            $paid >= $amount && $amount > 0 => 'paid',
            $dueDate && $dueDate->copy()->startOfMonth()->lt($today->copy()->startOfMonth()) && $paid > 0 => 'over_due_partial',
            $dueDate && $dueDate->copy()->startOfMonth()->lt($today->copy()->startOfMonth()) => 'over_due',
            $dueDate && $dueDate->lt($today) && $paid > 0 => 'due_partial',
            $dueDate && $dueDate->lt($today) => 'due',
            $paid > 0 => 'partial_paid',
            $amount > 0 => 'unpaid',
            default => 'paid',
        };
    }
}
