<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_exam_names', function (Blueprint $table) {
            if (! Schema::hasColumn('school_exam_names', 'exam_start_date')) {
                $table->date('exam_start_date')->nullable()->after('exam_name');
            }

            if (! Schema::hasColumn('school_exam_names', 'exam_end_date')) {
                $table->date('exam_end_date')->nullable()->after('exam_start_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('school_exam_names', function (Blueprint $table) {
            if (Schema::hasColumn('school_exam_names', 'exam_end_date')) {
                $table->dropColumn('exam_end_date');
            }

            if (Schema::hasColumn('school_exam_names', 'exam_start_date')) {
                $table->dropColumn('exam_start_date');
            }
        });
    }
};
