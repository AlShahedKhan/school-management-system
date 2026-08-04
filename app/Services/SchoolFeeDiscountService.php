<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class SchoolFeeDiscountService
{
    private array $sessionDiscountCache = [];

    private array $examDiscountCache = [];

    private array $templateCache = [];


    public function effectiveTotal(int $schoolId, int $studentId, float $amount, ?string $feesType, ?string $feeName): float
    {
        $total = $this->applySessionDiscount($schoolId, $studentId, $amount, $feeName);

        return $this->applyExamDiscount($schoolId, $studentId, $total, $feesType, $feeName);
    }

    private function applySessionDiscount(int $schoolId, int $studentId, float $amount, ?string $feeName): float
    {
        $key = $schoolId . ':' . $studentId;

        if (!array_key_exists($key, $this->sessionDiscountCache)) {
            $this->sessionDiscountCache[$key] = DB::table('school_fee_discounts')
                ->where('school_id', $schoolId)
                ->where('student_id', $studentId)
                ->where('discount_scope', 'session')
                ->get();
        }

        foreach ($this->sessionDiscountCache[$key] as $discount) {
            if ($discount->fee_name === $feeName && $discount->after_discount !== null) {
                return (float) $discount->after_discount;
            }
        }

        return $amount;
    }

    private function applyExamDiscount(int $schoolId, int $studentId, float $amount, ?string $feesType, ?string $feeName): float
    {
        $key = $schoolId . ':' . $studentId;

        if (!array_key_exists($key, $this->examDiscountCache)) {
            $this->examDiscountCache[$key] = DB::table('school_fee_discounts')
                ->where('school_id', $schoolId)
                ->where('student_id', $studentId)
                ->where('discount_scope', 'exam')
                ->orderByDesc('id')
                ->first();
        }

        $examDiscount = $this->examDiscountCache[$key];

        if (!$examDiscount) {
            return $amount;
        }



        if ($examDiscount->fee_type_id) {
            $discountFeeType = $this->feeTemplate($examDiscount->fee_type_id);

            if ($discountFeeType) {
                $typeMatches = $discountFeeType->fee_type_name === $feesType;
                $nameMatches = empty($discountFeeType->fee_name)
                    || empty($feeName)
                    || $discountFeeType->fee_name === $feeName;

                if (!$typeMatches || !$nameMatches) {
                    return $amount;
                }
            }
        }

        $qualifies = app(ExamDiscountApplicationService::class)
            ->studentQualifies($schoolId, $studentId, $examDiscount->minimum_grade);

        if (!$qualifies) {
            return $amount;
        }

        $value = (float) $examDiscount->discount_value;
        $discountAmount = $examDiscount->discount_type === 'Percentage'
            ? $amount * $value / 100
            : $value;

        return max($amount - $discountAmount, 0);
    }

    private function feeTemplate(int $id): ?object
    {
        if (!array_key_exists($id, $this->templateCache)) {
            $this->templateCache[$id] = DB::table('school_fee_templates')->where('id', $id)->first();
        }

        return $this->templateCache[$id];
    }
}
