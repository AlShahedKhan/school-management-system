<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();

            // Multi tenant SaaS relation
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();

            $table->date('date');
            $table->string('income_source');
            $table->string('name');
            $table->string('mobile');
            $table->decimal('amount', 10, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incomes');
    }
};
