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
        Schema::create('student_academic_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('session_id');
            $table->string('session_year', 10);
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('group_id')->nullable();
            $table->unsignedBigInteger('section_id');
            $table->string('roll_no', 50)->nullable();
            $table->string('academic_record_id', 50)->unique();
            $table->string('status', 20)->default('Active');
            $table->timestamps();

            // Foreign Key Constraints
            $table->foreign('school_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('admission_students')->onDelete('cascade');
            $table->foreign('session_id')->references('id')->on('school_sessions')->onDelete('cascade');
            $table->foreign('class_id')->references('id')->on('school_classes')->onDelete('cascade');
            $table->foreign('group_id')->references('id')->on('school_groups')->onDelete('set null');
            $table->foreign('section_id')->references('id')->on('school_sections')->onDelete('cascade');

            // Unique Composite Index: Prevent duplicate student record per school & session year
            $table->unique(['school_id', 'student_id', 'session_year'], 'unique_school_student_session');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_academic_records');
    }
};
