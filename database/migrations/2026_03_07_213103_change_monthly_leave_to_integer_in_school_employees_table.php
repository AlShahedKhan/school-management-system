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
        Schema::table('school_employees', function (Blueprint $table) {
            // Changing the type to integer (tinyInteger is best for 0-31 range)
            $table->tinyInteger('monthly_leave')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_employees', function (Blueprint $table) {
            // Reverting back to decimal if you ever need half-days again
            $table->decimal('monthly_leave', 10, 2)->default(0.00)->change();
        });
    }
};