<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_fee_discounts', function (Blueprint $table) {
            $table->decimal('before_discount', 12, 2)->nullable()->change();
            $table->decimal('discount_amount', 12, 2)->nullable()->change();
            $table->decimal('after_discount', 12, 2)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('school_fee_discounts', function (Blueprint $table) {
            $table->decimal('before_discount', 12, 2)->nullable(false)->change();
            $table->decimal('discount_amount', 12, 2)->nullable(false)->change();
            $table->decimal('after_discount', 12, 2)->nullable(false)->change();
        });
    }
};
