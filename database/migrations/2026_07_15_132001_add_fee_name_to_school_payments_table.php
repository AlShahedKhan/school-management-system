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
        Schema::table('school_payments', function (Blueprint $table) {
            $table->string('fee_name')->nullable()->after('fees_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_payments', function (Blueprint $table) {
            $table->dropColumn('fee_name');
        });
    }
};
