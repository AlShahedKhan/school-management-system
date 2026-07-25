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
        Schema::create('school_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('school_classes')->onDelete('cascade');
            $table->foreignId('group_id')->nullable()->constrained('school_groups')->onDelete('cascade');
            $table->foreignId('section_id')->nullable()->constrained('school_sections')->onDelete('cascade');
            $table->string('subject_name');
            $table->enum('subject_type', ['Theory', 'Theory+Practical']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_subjects');
    }
};