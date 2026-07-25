<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('school_student_fees', function (Blueprint $table) {
            $table->foreignId('fee_assign_id')->nullable()->after('fee_template_id')->constrained('school_fee_assigns')->onDelete('cascade');
            $table->string('generation_period')->nullable()->after('fee_assign_id');
            $table->decimal('discount_amount', 12, 2)->default(0)->after('amount');
            $table->decimal('payable_amount', 12, 2)->default(0)->after('discount_amount');
            $table->decimal('paid_amount', 12, 2)->default(0)->after('payable_amount');
            $table->decimal('due_amount', 12, 2)->default(0)->after('paid_amount');
            $table->decimal('advance_amount', 12, 2)->default(0)->after('due_amount');
            $table->date('due_date')->nullable()->after('pay_date');
        });

        DB::statement("ALTER TABLE school_student_fees MODIFY COLUMN status ENUM('unpaid', 'paid', 'partial_paid', 'due', 'partial_due', 'over_due', 'partial_over_due', 'advance', 'partial_advance') DEFAULT 'unpaid'");

        DB::statement("ALTER TABLE school_student_fees CHANGE amount base_amount DECIMAL(12, 2) DEFAULT 0.00");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_student_fees', function (Blueprint $table) {
            $table->dropForeign(['fee_assign_id']);
            $table->dropColumn([
                'fee_assign_id',
                'generation_period',
                'discount_amount',
                'payable_amount',
                'paid_amount',
                'due_amount',
                'advance_amount',
                'due_date'
            ]);
        });

        DB::statement("ALTER TABLE school_student_fees CHANGE base_amount amount DECIMAL(12, 2) DEFAULT 0.00");
        DB::statement("ALTER TABLE school_student_fees MODIFY COLUMN status ENUM('unpaid', 'paid', 'due', 'over_due') DEFAULT 'unpaid'");
    }
};
