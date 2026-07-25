<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_package_purchases', function (Blueprint $table) {
            $table->id();

            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->foreignId('sms_package_id')->constrained()->onDelete('cascade');

            $table->integer('total_sms');
            $table->integer('used_sms')->default(0);
            $table->integer('available_sms');

            $table->decimal('purchase_price', 10, 2);
            $table->decimal('sale_price', 10, 2);
            $table->decimal('profit', 10, 2);

            $table->date('purchase_date');
            $table->date('expiry_date');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_package_purchases');
    }
};
