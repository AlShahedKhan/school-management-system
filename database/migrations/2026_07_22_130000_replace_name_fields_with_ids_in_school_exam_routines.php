<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('school_exam_routines', function (Blueprint $table) {
            $table->unsignedBigInteger('class_id')->nullable()->after('total_hours');
            $table->unsignedBigInteger('group_id')->nullable()->after('class_id');
            $table->unsignedBigInteger('section_id')->nullable()->after('group_id');
            $table->unsignedBigInteger('session_id')->nullable()->after('section_id');
            $table->unsignedBigInteger('exam_id')->nullable()->after('session_id');
            $table->unsignedBigInteger('subject_id')->nullable()->after('exam_id');
        });

        Schema::table('school_exam_routines', function (Blueprint $table) {
            $table->dropColumn(['class_name', 'group_name', 'section_name', 'session_name', 'exam_name', 'subject_name']);
        });

        Schema::table('school_exam_routines', function (Blueprint $table) {
            $table->foreign('class_id')->references('id')->on('school_classes')->onDelete('set null');
            $table->foreign('group_id')->references('id')->on('school_groups')->onDelete('set null');
            $table->foreign('section_id')->references('id')->on('school_sections')->onDelete('set null');
            $table->foreign('session_id')->references('id')->on('school_sessions')->onDelete('set null');
            $table->foreign('exam_id')->references('id')->on('school_exam_names')->onDelete('set null');
            $table->foreign('subject_id')->references('id')->on('school_subjects')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('school_exam_routines', function (Blueprint $table) {
            $table->dropForeign(['class_id']);
            $table->dropForeign(['group_id']);
            $table->dropForeign(['section_id']);
            $table->dropForeign(['session_id']);
            $table->dropForeign(['exam_id']);
            $table->dropForeign(['subject_id']);
        });

        Schema::table('school_exam_routines', function (Blueprint $table) {
            $table->string('class_name')->nullable()->after('total_hours');
            $table->string('group_name')->nullable()->after('class_name');
            $table->string('section_name')->nullable()->after('group_name');
            $table->string('session_name')->nullable()->after('section_name');
            $table->string('exam_name')->nullable()->after('session_name');
            $table->string('subject_name')->nullable()->after('exam_name');
        });

        Schema::table('school_exam_routines', function (Blueprint $table) {
            $table->dropColumn(['class_id', 'group_id', 'section_id', 'session_id', 'exam_id', 'subject_id']);
        });
    }
};
