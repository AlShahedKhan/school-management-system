<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_holidays', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->enum('type', ['Class Wise', 'General']);
            
            // Class specific fields
            $table->string('class_name')->nullable();
            $table->string('group_name')->nullable();
            $table->string('section_name')->nullable();
            $table->string('session')->nullable();
            
            // Holiday details
            $table->string('reason');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('total_days');
            
            $table->timestamps();

            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_holidays');
    }
};