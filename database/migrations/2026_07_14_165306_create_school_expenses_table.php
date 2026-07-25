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
        Schema::create('school_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('invoice_no')->unique();
            $table->date('expense_date');
            $table->string('expense_reason');
            $table->decimal('amount', 12, 2);
            $table->decimal('balance', 12, 2)->default(0);
            $table->timestamps();
            $table->index(['school_id', 'expense_date']);
            $table->index('invoice_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_expenses');
    }
};
