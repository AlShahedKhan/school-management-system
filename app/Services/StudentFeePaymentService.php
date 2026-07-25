<?php

namespace App\Services;

use App\Models\SchoolStudentFee;
use App\Models\SchoolPayment;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class StudentFeePaymentService
{
    /**
     * Process a payment against a specific fee.
     */
    public function processPayment(SchoolStudentFee $fee, $paymentAmount, $paymentMethod, $transactionId = null)
    {
        if ($paymentAmount <= 0) {
            throw new Exception("Payment amount must be greater than zero.");
        }

        DB::transaction(function () use ($fee, $paymentAmount, $paymentMethod, $transactionId) {
            // Update the fee ledger
            $fee->paid_amount += $paymentAmount;
            
            if ($fee->paid_amount > $fee->payable_amount) {
                $fee->advance_amount = $fee->paid_amount - $fee->payable_amount;
                $fee->due_amount = 0;
            } else {
                $fee->advance_amount = 0;
                $fee->due_amount = $fee->payable_amount - $fee->paid_amount;
            }

            // Determine status based on payments and due date
            $fee->status = $this->determineStatus($fee);
            $fee->save();

            // Record atomic payment history
            SchoolPayment::create([
                'school_id' => $fee->school_id,
                'student_id' => $fee->student_id,
                'school_student_fee_id' => $fee->id, // If you have this column in your payments table
                'fee_name' => $fee->fee_name ?? $fee->fee_type_name,
                'amount' => $paymentAmount,
                'payment_method' => $paymentMethod,
                'transaction_id' => $transactionId,
                'payment_date' => Carbon::now()->toDateString(),
            ]);
        });
    }

    /**
     * Determine dynamic status of a fee record.
     */
    public function determineStatus(SchoolStudentFee $fee)
    {
        if ($fee->paid_amount == 0) {
            if ($fee->due_date && Carbon::now()->startOfDay()->greaterThan(Carbon::parse($fee->due_date)->startOfDay())) {
                return 'due'; // Note: an external cron will change this to 'over_due' on cycle change.
            }
            return 'unpaid';
        }

        if ($fee->paid_amount < $fee->payable_amount) {
            if ($fee->due_date && Carbon::now()->startOfDay()->greaterThan(Carbon::parse($fee->due_date)->startOfDay())) {
                return 'partial_due';
            }
            return 'partial_paid';
        }

        if ($fee->paid_amount == $fee->payable_amount) {
            return 'paid';
        }

        if ($fee->paid_amount > $fee->payable_amount) {
            return 'advance';
        }

        return $fee->status;
    }
}
