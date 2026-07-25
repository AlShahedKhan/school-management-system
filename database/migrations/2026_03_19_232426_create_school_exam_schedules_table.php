<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_exam_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->string('class_name');
            $table->string('group_name')->nullable();
            $table->string('section_name')->nullable();
            $table->string('session_name');
            $table->string('exam_name');
            $table->integer('total_subject')->default(0);
            $table->integer('submitted_subject')->default(0);
            $table->integer('remaining_subject')->default(0);
            $table->date('publish_date');
            $table->time('publish_time');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_exam_schedules');
    }
};