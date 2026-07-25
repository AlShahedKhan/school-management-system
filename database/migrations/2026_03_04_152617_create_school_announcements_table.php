<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_announcements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->enum('type', ['Class Wise', 'General']);
            
            // Class specific fields (Nullable for General)
            $table->string('class_name')->nullable();
            $table->string('group_name')->nullable();
            $table->string('section_name')->nullable();
            $table->string('session')->nullable();
            
            // Common fields
            $table->string('title');
            $table->text('details');
            $table->date('date');
            
            $table->timestamps();

            // Foreign key to schools table (assuming 'schools' table exists)
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_announcements');
    }
};