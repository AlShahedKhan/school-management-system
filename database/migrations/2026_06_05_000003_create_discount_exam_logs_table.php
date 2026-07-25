<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discount_exam_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('discount_student_id')->constrained('discount_students')->onDelete('cascade');
            $table->foreignId('exam_id')->constrained('school_exam_names')->onDelete('cascade');

            $table->decimal('exam_gpa', 4, 2)->nullable();
            $table->decimal('exam_marks', 5, 2)->nullable();
            $table->boolean('passed');

            $table->enum('action_taken', ['granted', 'cancelled', 'resumed']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discount_exam_logs');
    }
};
