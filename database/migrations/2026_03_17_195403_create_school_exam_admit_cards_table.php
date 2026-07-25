<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_exam_admit_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->string('class_name');
            $table->string('group_name')->nullable();
            $table->string('section_name')->nullable();
            $table->string('session_name');
            $table->string('exam_name');
            $table->string('student_id_number');
            $table->string('admit_card_start_number'); // Added per your form requirement
            $table->string('admit_card_end_number');   // Added per your form requirement
            $table->string('admit_card_number')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_exam_admit_cards');
    }
};