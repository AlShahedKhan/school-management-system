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
        Schema::table('school_subjects', function (Blueprint $table) {
            if (!Schema::hasColumn('school_subjects', 'grade_id')) {
                $table->foreignId('grade_id')->nullable()->after('subject_type')->constrained('school_exam_grades')->onDelete('cascade');
            }
            if (!Schema::hasColumn('school_subjects', 'subject_code')) {
                $table->string('subject_code')->nullable()->after('subject_name');
            }
            if (!Schema::hasColumn('school_subjects', 'marks')) {
                $table->json('marks')->nullable()->after('subject_code');
            }
            
            // Drop old columns if they exist
            if (Schema::hasColumn('school_subjects', 'full_marks')) {
                $table->dropColumn('full_marks');
            }
            if (Schema::hasColumn('school_subjects', 'pass_marks')) {
                $table->dropColumn('pass_marks');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_subjects', function (Blueprint $table) {
            if (Schema::hasColumn('school_subjects', 'grade_id')) {
                $table->dropForeign(['grade_id']);
                $table->dropColumn('grade_id');
            }
            if (Schema::hasColumn('school_subjects', 'subject_code')) {
                $table->dropColumn('subject_code');
            }
            if (Schema::hasColumn('school_subjects', 'marks')) {
                $table->dropColumn('marks');
            }
            
            // Restore old columns if needed (assuming they were strings or something)
            // But usually we don't restore old data in down if not necessary.
            // Let's just add them back as strings if they existed.
            if (!Schema::hasColumn('school_subjects', 'full_marks')) {
                $table->string('full_marks')->nullable();
            }
            if (!Schema::hasColumn('school_subjects', 'pass_marks')) {
                $table->string('pass_marks')->nullable();
            }
        });
    }
};
