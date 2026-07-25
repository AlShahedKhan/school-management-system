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
        Schema::create('donates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('donate_no')->unique();
            $table->string('name');
            $table->string('mobile_number', 20);
            $table->string('location')->nullable();
            $table->string('donate_reason');
            $table->decimal('amount', 12, 2);
            $table->text('note')->nullable();
            $table->timestamps();
            $table->index('donate_no');
            $table->index('mobile_number');
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donates');
    }
};
