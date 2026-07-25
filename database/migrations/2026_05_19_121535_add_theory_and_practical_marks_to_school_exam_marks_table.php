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
        Schema::table('school_exam_marks', function (Blueprint $table) {
            $table->decimal('theory_mark', 8, 2)->nullable()->after('mark');
            $table->decimal('practical_mark', 8, 2)->nullable()->after('theory_mark');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_exam_marks', function (Blueprint $table) {
            $table->dropColumn(['theory_mark', 'practical_mark']);
        });
    }
};
