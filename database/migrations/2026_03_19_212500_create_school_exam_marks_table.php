<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('school_exam_marks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id')->index();
            $table->string('class_name')->index();
            $table->string('group_name')->nullable();
            $table->string('section_name')->nullable();
            $table->string('session_name')->index();
            $table->string('exam_name')->index();
            $table->string('subject_name')->index();
            $table->string('student_id_number')->index();
            $table->string('student_name');
            $table->string('roll_no')->nullable();
            $table->decimal('mark', 5, 2)->default(0);
            $table->string('letter_name')->nullable();
            $table->decimal('point', 4, 2)->default(0);
            $table->enum('status', ['draft', 'published'])->default('published');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('school_exam_marks');
    }
};