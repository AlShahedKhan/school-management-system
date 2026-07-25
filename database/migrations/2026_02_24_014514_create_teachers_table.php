<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->string('name');
            $table->string('designation')->nullable();
            $table->string('id_number')->unique();
            $table->string('mobile')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->date('dob')->nullable();
            $table->string('photo')->nullable();
            $table->timestamps();

            // Foreign key
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
