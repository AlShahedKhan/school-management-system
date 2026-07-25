<?php

namespace App\Services;

use App\Models\SchoolFeeTemplate;
use App\Models\AdmissionStudent;
use App\Models\SchoolDiscount;

class SchoolFeeDiscountService
{
    /**
     * Calculate payable amount after applying dynamic discounts.
     * Formula: max(0, Fee Amount - Discount)
     */
    public function calculatePayableAmount(SchoolFeeTemplate $template, AdmissionStudent $student)
    {
        $baseAmount = $template->amount;
        $discountAmount = 0;

        // Fetch discounts that might apply to this student for this fee template.
        // Assuming there is a logic for discounts based on fee_assign_id and student_id.
        // For demonstration, let's assume we have active discounts assigned to the student for this category.
        // Needs adjustment based on actual Discount model structure.

        // Example logic:
        // $discounts = SchoolDiscount::where('student_id', $student->id)
        //    ->where('fee_assign_id', $template->fee_assign_id)
        //    ->get();
        // 
        // foreach ($discounts as $discount) {
        //     if ($discount->discount_type === 'Percentage') {
        //         $discountAmount += ($baseAmount * $discount->discount_value / 100);
        //     } else {
        //         $discountAmount += $discount->discount_value;
        //     }
        // }

        $payable = max(0, $baseAmount - $discountAmount);

        return [
            'payable_amount' => $payable,
            'discount_amount' => $discountAmount,
        ];
    }
}
