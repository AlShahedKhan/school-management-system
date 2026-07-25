<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('school_subscriptions', function (Blueprint $col) {
            // Upgrade Category
            $col->enum('upgrade_type', ['none', 'sale', 'contract', 'subscription'])->default('none')->after('status');

            // Custom Limits (Overrides package defaults)
            $col->integer('student_limit')->nullable()->after('upgrade_type');
            $col->integer('teacher_limit')->nullable()->after('student_limit');

            // Pricing Overrides
            $col->decimal('per_student_price', 10, 2)->nullable()->after('teacher_limit');

            // For 'Sale' or 'Contract' specific tracking
            $col->date('sale_date')->nullable()->after('per_student_price');
            $col->date('contract_close_date')->nullable()->after('sale_date');

            // Note: final_price and expiry_date already exist in your table,
            // so we will reuse them for "Total Payable" and "Close Date".
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_subscriptions', function (Blueprint $col) {
            $col->dropColumn([
                'upgrade_type',
                'student_limit',
                'teacher_limit',
                'per_student_price',
                'sale_date',
                'contract_close_date'
            ]);
        });
    }
};
