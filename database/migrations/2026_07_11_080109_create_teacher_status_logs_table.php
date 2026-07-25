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
        Schema::create('teacher_status_logs', function (Blueprint $table) {
            // Added on 2026-07-11: Teacher status tracking history fields
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->string('status'); 
            $table->string('action'); 
            $table->foreignId('changed_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('replacement_teacher_id')->nullable()->constrained('teachers')->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_status_logs');
    }
};
