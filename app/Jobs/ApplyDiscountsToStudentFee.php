<?php

namespace App\Jobs;

use App\Models\DiscountStudent;
use App\Models\SchoolStudentFee;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ApplyDiscountsToStudentFee implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $schoolId,
        public int $studentId,
        public int $feeTemplateId,
        public int $studentFeeId,
    ) {}

    public function handle(): void
    {
        $studentFee = SchoolStudentFee::find($this->studentFeeId);
        if (!$studentFee) return;

        $feeTemplate = $studentFee->feeTemplate;
        if (!$feeTemplate) return;

        $discountStudents = DiscountStudent::with('discount')
            ->where('student_id', $this->studentId)
            ->where('status', 'active')
            ->get();

        foreach ($discountStudents as $ds) {
            $discount = $ds->discount;
            if (!$discount || !$discount->is_active) continue;

            if ($discount->fee_template_id && $discount->fee_template_id != $this->feeTemplateId) {
                continue;
            }

            if ($discount->discount_category === 'specific_months') {
                $currentMonth = Carbon::now()->format('Y-m');
                $months = $discount->months ?? [];
                if (!in_array($currentMonth, $months)) {
                    continue;
                }
            }

            if ($discount->discount_category === 'exam_waiver' && $ds->status !== 'active') {
                continue;
            }

            $originalAmount = (float) $studentFee->amount;
            $discountValue = (float) $discount->discount_value;

            if ($discount->discount_type === 'Percentage') {
                $newAmount = $originalAmount - ($originalAmount * $discountValue / 100);
            } else {
                $newAmount = max($originalAmount - $discountValue, 0);
            }

            $ds->update([
                'before_amount' => $originalAmount,
                'after_amount'  => round($newAmount, 2),
            ]);

            return;
        }
    }
}
