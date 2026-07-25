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
        Schema::create('teacher_academic_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('teacher_id');
            $table->unsignedBigInteger('session_id')->nullable();
            $table->string('session_year', 10)->nullable();
            $table->string('designation', 255)->nullable();
            $table->string('academic_record_id', 50)->unique();
            $table->string('status', 20)->default('Active');
            $table->timestamps();

            // Foreign Key Constraints
            $table->foreign('school_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('cascade');

            // Unique Composite Index
            $table->unique(['school_id', 'teacher_id', 'session_year'], 'unique_school_teacher_session');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_academic_records');
    }
};
