<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Added on 2026-07-09: Migration for student re-admission history logs
     */
    public function up(): void
    {
        Schema::create('student_readmissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id')->index();
            $table->unsignedBigInteger('student_id')->index();
            $table->unsignedBigInteger('class_id')->index();
            $table->unsignedBigInteger('from_group_id')->nullable();
            $table->unsignedBigInteger('to_group_id')->nullable();
            $table->unsignedBigInteger('from_section_id')->index();
            $table->unsignedBigInteger('to_section_id')->index();
            $table->unsignedBigInteger('from_session_id')->index();
            $table->unsignedBigInteger('to_session_id')->index();
            $table->string('student_id_number')->index();
            $table->string('from_admission_id')->index();
            $table->string('to_admission_id')->index();
            $table->date('readmission_date');
            $table->decimal('readmission_fee', 10, 2)->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_readmissions');
    }
};
