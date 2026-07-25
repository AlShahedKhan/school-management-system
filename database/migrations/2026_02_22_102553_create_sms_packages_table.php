<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_packages', function (Blueprint $table) {
            $table->id();

            // Package Info
            $table->string('name'); // Example: Starter 5K
            $table->integer('sms_quantity'); // Total SMS in this package
            $table->integer('validity_days'); // Expiry days

            // Pricing
            $table->decimal('purchase_price', 10, 2); // Your buying price from provider
            $table->decimal('sale_price', 10, 2); // Your selling price to school

            // Auto Calculated (Optional stored)
            $table->decimal('profit_per_package', 10, 2)->default(0);

            // Status
            $table->boolean('status')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_packages');
    }
};
