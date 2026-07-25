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
        Schema::table('school_fee_types', function (Blueprint $table) {
            // fee_name for Admission, Monthly & Others
            $table->string('fee_name')->nullable()->after('fee_type_name');

            // Foreign key for Exam Fee
            $table->unsignedBigInteger('exam_id')->nullable()->after('fee_name');
            
            // Foreign key for Boarding Food (linked to admission_students)
            $table->unsignedBigInteger('student_id')->nullable()->after('exam_id');

            // Foreign Key Constraints
            $table->foreign('exam_id')
                  ->references('id')
                  ->on('school_exam_names')
                  ->onDelete('set null');

            $table->foreign('student_id')
                  ->references('id')
                  ->on('admission_students') // Updated to your specific table name
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_fee_types', function (Blueprint $table) {
            // Drop foreign keys first to avoid errors
            $table->dropForeign(['exam_id']);
            $table->dropForeign(['student_id']);
            
            // Drop the columns
            $table->dropColumn(['fee_name', 'exam_id', 'student_id']);
        });
    }
};