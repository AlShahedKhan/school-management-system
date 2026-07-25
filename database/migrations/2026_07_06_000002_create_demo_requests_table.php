<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demo_requests', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('school_name', 160);
            $table->string('phone', 30);
            $table->string('email', 160)->nullable();
            $table->text('message')->nullable();
            $table->string('status', 30)->default('new');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demo_requests');
    }
};
