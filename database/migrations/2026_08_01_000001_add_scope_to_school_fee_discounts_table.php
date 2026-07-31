<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_fee_discounts', function (Blueprint $table) {
            if (!Schema::hasColumn('school_fee_discounts', 'discount_scope')) {
                $table->string('discount_scope')->default('session')->after('session_id');
            }
            if (!Schema::hasColumn('school_fee_discounts', 'minimum_grade')) {
                $table->string('minimum_grade')->nullable()->after('discount_value');
            }
            if (Schema::hasColumn('school_fee_discounts', 'start_date')) {
                $table->dropColumn('start_date');
            }
            if (Schema::hasColumn('school_fee_discounts', 'end_date')) {
                $table->dropColumn('end_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('school_fee_discounts', function (Blueprint $table) {
            if (Schema::hasColumn('school_fee_discounts', 'discount_scope')) {
                $table->dropColumn('discount_scope');
            }
            if (Schema::hasColumn('school_fee_discounts', 'minimum_grade')) {
                $table->dropColumn('minimum_grade');
            }
            if (!Schema::hasColumn('school_fee_discounts', 'start_date')) {
                $table->date('start_date')->nullable();
            }
            if (!Schema::hasColumn('school_fee_discounts', 'end_date')) {
                $table->date('end_date')->nullable();
            }
        });
    }
};
