<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\SchoolPayment;
use App\Models\SchoolStudentFee;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SchoolPaymentSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::query()
            ->where('email', 'school@test.com')
            ->orWhere('school_name', 'Green Valley School')
            ->first();

        if (! $school) {
            return;
        }

        SchoolStudentFee::query()
            ->where('school_id', $school->id)
            ->where('paid_amount', '>', 0)
            ->orderBy('id')
            ->take(15)
            ->get()
            ->each(function (SchoolStudentFee $fee, int $index): void {
                $paidAmount = (float) $fee->paid_amount;
                $dueAmount = max((float) $fee->due_amount, 0);
                $payDate = Carbon::parse($fee->pay_date ?? now())->subDays($index % 5);

                SchoolPayment::updateOrCreate(
                    [
                        'school_student_fee_id' => $fee->id,
                        'pay_date' => $payDate->toDateString(),
                    ],
                    [
                        'school_id' => $fee->school_id,
                        'admission_student_id' => $fee->student_id,
                        'fees_type' => $fee->fee_type_name,
                        'fee_name' => $fee->fee_name,
                        'total_payable' => $fee->payable_amount,
                        'payable_due' => $dueAmount,
                        'total_amount' => $paidAmount,
                        'total_due' => $dueAmount,
                        'pay_type' => $dueAmount > 0 ? 'Due' : 'Payable',
                        'type_amount' => $paidAmount,
                        'for_month' => $fee->generation_period,
                        'pay_method' => ['Cash', 'Bank', 'Mobile'][$index % 3],
                        'created_at' => $payDate->copy()->setTime(9 + ($index % 6), 0),
                        'updated_at' => $payDate->copy()->setTime(9 + ($index % 6), 0),
                    ]
                );
            });
    }
}
