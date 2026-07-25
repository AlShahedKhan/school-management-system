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
        Schema::table('school_fee_types', function (Blueprint $blueprint) {
            // Adding the pay_date column after fee_type_name
            $blueprint->date('pay_date')->nullable()->after('fee_type_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_fee_types', function (Blueprint $blueprint) {
            $blueprint->dropColumn('pay_date');
        });
    }
};