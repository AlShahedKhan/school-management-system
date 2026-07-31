<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_fee_discounts', function (Blueprint $table) {
            $table->dropForeign('school_fee_discounts_fee_type_id_foreign');
            $table->foreign('fee_type_id')->references('id')->on('school_fee_templates')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('school_fee_discounts', function (Blueprint $table) {
            $table->dropForeign('school_fee_discounts_fee_type_id_foreign');
            $table->foreign('fee_type_id')->references('id')->on('school_fee_types')->onDelete('cascade');
        });
    }
};
