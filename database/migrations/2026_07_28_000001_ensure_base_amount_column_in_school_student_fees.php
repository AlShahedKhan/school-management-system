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
        if (Schema::hasColumn('school_student_fees', 'amount') && !Schema::hasColumn('school_student_fees', 'base_amount')) {
            DB::statement("ALTER TABLE school_student_fees CHANGE amount base_amount DECIMAL(12, 2) DEFAULT 0.00");
        } elseif (!Schema::hasColumn('school_student_fees', 'base_amount')) {
            Schema::table('school_student_fees', function (Blueprint $table) {
                $table->decimal('base_amount', 12, 2)->default(0)->after('fee_name');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Keep base_amount column intact
    }
};
