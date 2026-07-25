<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->string('class_name'); // Primary focus
            $table->string('group')->nullable();
            $table->string('section')->nullable();
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
        Schema::dropIfExists('school_classes');
    }
};