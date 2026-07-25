<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('class_id')->constrained('school_classes')->onDelete('cascade');
            $table->string('group_name'); 
            $table->string('section')->nullable();
            $table->string('session')->nullable();
            $table->string('roll')->nullable();
            $table->string('student_id_number')->nullable();
            $table->string('student_name')->nullable();
            $table->decimal('total_payable', 12, 2)->nullable()->default(0);
            $table->decimal('payable_due', 12, 2)->nullable()->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_groups');
    }
};