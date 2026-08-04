<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_admit_card_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->unique()->constrained()->cascadeOnDelete();
            $table->json('instructions_en');
            $table->json('instructions_bn');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_admit_card_settings');
    }
};
