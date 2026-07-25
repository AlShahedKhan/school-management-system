<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('school_exam_routines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->date('exam_date');
            $table->string('day_name');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('total_hours');
            $table->string('class_name');
            $table->string('group_name');
            $table->string('section_name');
            $table->string('session_name');
            $table->string('exam_name');
            $table->string('subject_name');
            $table->timestamps();

            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('school_exam_routines');
    }
};