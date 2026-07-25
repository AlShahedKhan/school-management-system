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
        Schema::table('school_exam_names', function (Blueprint $table) {
            // Check if column exists before dropping to avoid errors during deployment
            if (Schema::hasColumn('school_exam_names', 'exam_fee')) {
                $table->dropColumn('exam_fee');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_exam_names', function (Blueprint $table) {
            // Restore the column if the migration is rolled back
            $table->decimal('exam_fee', 10, 2)->default(0.00)->after('exam_name');
        });
    }
};