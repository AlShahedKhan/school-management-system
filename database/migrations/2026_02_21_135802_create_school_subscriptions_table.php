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
        Schema::create('school_subscriptions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('package_id')->constrained()->cascadeOnDelete();

            $table->enum('duration_months', [2, 4, 12]);

            $table->decimal('original_price', 12, 2);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->decimal('final_price', 12, 2);

            $table->date('start_date');
            $table->date('expiry_date');

            $table->enum('status', ['active', 'expired', 'cancelled'])
                ->default('active');

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_subscriptions');
    }
};
