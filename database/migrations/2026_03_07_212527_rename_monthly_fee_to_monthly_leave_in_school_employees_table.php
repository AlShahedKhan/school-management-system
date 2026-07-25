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
            // Renaming the column from monthly_fee to monthly_leave
            $table->renameColumn('monthly_fee', 'monthly_leave');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_employees', function (Blueprint $table) {
            // Reverting the name back if the migration is rolled back
            $table->renameColumn('monthly_leave', 'monthly_fee');
        });
    }
};