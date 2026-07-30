<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_exam_marks', function (Blueprint $table) {
            if (! Schema::hasColumn('school_exam_marks', 'tutorial_mark')) {
                $table->decimal('tutorial_mark', 8, 2)->default(0)->after('mark');
            }
            if (! Schema::hasColumn('school_exam_marks', 'mcq_mark')) {
                $table->decimal('mcq_mark', 8, 2)->default(0)->after('tutorial_mark');
            }
            if (! Schema::hasColumn('school_exam_marks', 'writing_mark')) {
                $table->decimal('writing_mark', 8, 2)->default(0)->after('mcq_mark');
            }
        });
    }

    public function down(): void
    {
        Schema::table('school_exam_marks', function (Blueprint $table) {
            foreach (['tutorial_mark', 'mcq_mark', 'writing_mark'] as $column) {
                if (Schema::hasColumn('school_exam_marks', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
