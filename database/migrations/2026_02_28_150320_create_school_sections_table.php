<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('class_id')->constrained('school_classes')->onDelete('cascade');
            $table->foreignId('group_id')->nullable()->constrained('school_groups')->onDelete('cascade');
            $table->string('section_name'); 
            $table->string('session')->nullable();
            $table->string('roll')->nullable();
            $table->string('student_id_number')->nullable();
            $table->string('student_name')->nullable();
            $table->decimal('total_fees', 12, 2)->nullable()->default(0);
            $table->decimal('fees_due', 12, 2)->nullable()->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_sections');
    }
};