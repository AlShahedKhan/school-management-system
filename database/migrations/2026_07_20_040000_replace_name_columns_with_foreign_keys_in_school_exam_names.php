<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_exam_names', function (Blueprint $table) {
            $table->unsignedBigInteger('class_id')->nullable()->after('school_id');
            $table->unsignedBigInteger('group_id')->nullable()->after('class_id');
            $table->unsignedBigInteger('section_id')->nullable()->after('group_id');
            $table->unsignedBigInteger('session_id')->nullable()->after('section_id');

            $table->foreign('class_id')->references('id')->on('school_classes')->onDelete('set null');
            $table->foreign('group_id')->references('id')->on('school_groups')->onDelete('set null');
            $table->foreign('section_id')->references('id')->on('school_sections')->onDelete('set null');
            $table->foreign('session_id')->references('id')->on('school_sessions')->onDelete('set null');
        });

        Schema::table('school_exam_names', function (Blueprint $table) {
            $table->dropColumn(['class_name', 'group_name', 'section_name', 'session_name']);
        });
    }

    public function down(): void
    {
        Schema::table('school_exam_names', function (Blueprint $table) {
            $table->string('class_name')->nullable();
            $table->string('group_name')->nullable();
            $table->string('section_name')->nullable();
            $table->string('session_name')->nullable();
        });

        Schema::table('school_exam_names', function (Blueprint $table) {
            $table->dropForeign(['class_id']);
            $table->dropForeign(['group_id']);
            $table->dropForeign(['section_id']);
            $table->dropForeign(['session_id']);
            $table->dropColumn(['class_id', 'group_id', 'section_id', 'session_id']);
        });
    }
};
