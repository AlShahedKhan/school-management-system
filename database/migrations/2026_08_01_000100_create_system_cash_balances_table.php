<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_cash_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->unique()->constrained('schools')->onDelete('cascade');
            $table->decimal('opening_balance', 18, 2)->default(0);
            $table->decimal('current_balance', 18, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_cash_balances');
    }
};
