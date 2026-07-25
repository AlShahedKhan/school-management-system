<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Created/Modified on 2026-07-06: Migration for student promotion history
     */
    public function up(): void
    {
        Schema::create('student_promotions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('from_class_id');
            $table->unsignedBigInteger('from_group_id')->nullable();
            $table->unsignedBigInteger('from_section_id');
            $table->unsignedBigInteger('from_session_id');
            $table->string('from_student_id_number');
            $table->unsignedBigInteger('to_class_id');
            $table->unsignedBigInteger('to_group_id')->nullable();
            $table->unsignedBigInteger('to_section_id');
            $table->unsignedBigInteger('to_session_id');
            $table->string('to_student_id_number');
            $table->date('promote_date');
            $table->decimal('promote_fee', 10, 2)->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_promotions');
    }
};
