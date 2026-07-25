<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_exam_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->string('grade_name');
            $table->string('grade_point')->nullable();           
            $table->decimal('full_mark', 5, 2)->nullable();
            $table->decimal('mark_from', 5, 2)->nullable();
            $table->decimal('mark_to', 5, 2)->nullable(); 
            $table->string('note')->nullable(); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_exam_grades');
    }
};