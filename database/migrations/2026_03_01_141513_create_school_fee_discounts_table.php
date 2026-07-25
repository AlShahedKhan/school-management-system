<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_fee_discounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('class_id')->constrained('school_classes')->onDelete('cascade');
            $table->foreignId('group_id')->nullable()->constrained('school_groups')->onDelete('cascade');
            $table->foreignId('section_id')->nullable()->constrained('school_sections')->onDelete('cascade');
            $table->foreignId('session_id')->constrained('school_sessions')->onDelete('cascade');
            
            // Fixed: Pointing to admission_students table
            $table->foreignId('student_id')->constrained('admission_students')->onDelete('cascade');
            
            $table->foreignId('fee_type_id')->constrained('school_fee_types')->onDelete('cascade');
            
            $table->string('discount_type'); // Fixed or Percentage
            $table->decimal('discount_value', 12, 2);
            $table->decimal('before_discount', 12, 2);
            $table->decimal('discount_amount', 12, 2);
            $table->decimal('after_discount', 12, 2);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_fee_discounts');
    }
};