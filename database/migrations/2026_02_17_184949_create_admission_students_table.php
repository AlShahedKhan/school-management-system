<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admission_students', function (Blueprint $table) {
            $table->id();

            // Step 1
            $table->string('division');
            $table->string('district');
            $table->string('upazila');

            $table->string('school');
            $table->string('class');
            $table->string('group');
            $table->string('session');
            $table->string('admission_fee');
            $table->date('admission_date');

            // Step 2
            $table->string('previous_school');
            $table->string('previous_class');
            $table->string('previous_group');
            $table->string('previous_section');
            $table->string('previous_session');
            $table->string('interview_code');

            // Step 3
            $table->unsignedBigInteger('guardian_id');

            // Step 4
            $table->string('student_name');
            $table->string('father_name');
            $table->string('mother_name');
            $table->string('student_id_number');

            $table->string('current_division');
            $table->string('current_district');
            $table->string('current_upazila');
            $table->string('current_village');

            $table->string('permanent_division');
            $table->string('permanent_district');
            $table->string('permanent_upazila');
            $table->string('permanent_village');

            $table->string('mobile');
            $table->string('password');

            $table->string('image')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admission_students');
    }
};
