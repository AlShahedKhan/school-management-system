<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_exam_seat_plans', function (Blueprint $col) {
            $col->id();
            $col->unsignedBigInteger('school_id');
            $col->string('class_name');
            $col->string('group_name')->nullable();
            $col->string('section_name')->nullable();
            $col->string('session_name');
            $col->string('exam_name');
            $col->string('student_id_number');
            $col->string('seat_number');
            $col->string('seat_number_start'); // Reference for the batch
            $col->string('seat_number_end');   // Reference for the batch
            $col->timestamps();

            // Unique constraint to prevent duplicate seat plans for the same student/exam
            $col->unique(['school_id', 'exam_name', 'student_id_number'], 'unique_seat_plan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_exam_seat_plans');
    }
};