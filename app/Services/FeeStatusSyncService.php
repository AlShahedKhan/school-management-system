<?php

namespace App\Services;

use App\Models\SchoolPayment;
use App\Models\SchoolStudentFee;
use Carbon\Carbon;

class FeeStatusSyncService
{
    public function syncPending(?int $schoolId = null): int
    {
        $query = SchoolStudentFee::where('pay_date', '<', Carbon::today())
            ->where('status', 'pending');

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
        $amount = (float) $fee->amount;
        $today = Carbon::today();
        $payDate = $fee->pay_date ? Carbon::parse($fee->pay_date) : null;

        return match (true) {
            $paid >= $amount && $amount > 0 => 'paid',
            $payDate && $payDate->copy()->startOfMonth()->lt($today->copy()->startOfMonth()) && $paid > 0 => 'over_due_partial',
            $payDate && $payDate->copy()->startOfMonth()->lt($today->copy()->startOfMonth()) => 'over_due',
            $payDate && $payDate->lt($today) && $paid > 0 => 'due_partial',
            $payDate && $payDate->lt($today) => 'due',
            $paid > 0 => 'partial_paid',
            default => 'pending',
        };
    }
}
