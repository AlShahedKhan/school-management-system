<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_syllabuses', function (Blueprint $table) {
            $table->unsignedBigInteger('exam_id')->nullable()->after('subject_id');
            $table->foreign('exam_id')->references('id')->on('school_exam_names')->onDelete('set null');
        });

        DB::statement("UPDATE school_syllabuses s
            JOIN school_exam_names e ON e.exam_name = s.exam_name
                AND e.school_id = s.school_id
                AND e.session_id = s.session_id
                AND e.class_id = s.class_id
                AND (e.group_id = s.group_id OR (e.group_id IS NULL AND s.group_id IS NULL))
                AND (e.section_id = s.section_id OR (e.section_id IS NULL AND s.section_id IS NULL))
            SET s.exam_id = e.id");

        Schema::table('school_syllabuses', function (Blueprint $table) {
            $table->dropColumn('exam_name');
        });
    }

    public function down(): void
    {
        Schema::table('school_syllabuses', function (Blueprint $table) {
            $table->string('exam_name')->nullable()->after('subject_id');
        });

        DB::statement("UPDATE school_syllabuses s
            JOIN school_exam_names e ON e.id = s.exam_id
            SET s.exam_name = e.exam_name");

        Schema::table('school_syllabuses', function (Blueprint $table) {
            $table->dropForeign(['exam_id']);
            $table->dropColumn('exam_id');
        });
    }
};
