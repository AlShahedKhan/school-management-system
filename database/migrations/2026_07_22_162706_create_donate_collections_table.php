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
        Schema::create('donate_collections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('donate_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->decimal('paid_amount', 12, 2);
            $table->unsignedTinyInteger('receive_month');
            $table->unsignedSmallInteger('receive_year');
            $table->date('receive_date');
            $table->text('note')->nullable();
            $table->timestamps();
            $table->index(['receive_month', 'receive_year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donate_collections');
    }
};
