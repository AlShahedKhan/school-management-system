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
        Schema::table('school_fee_discounts', function (Blueprint $blueprint) {
            // Adding the nullable fee_name column
            $blueprint->string('fee_name')->nullable()->after('fee_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_fee_discounts', function (Blueprint $blueprint) {
            $blueprint->dropColumn('fee_name');
        });
    }
};