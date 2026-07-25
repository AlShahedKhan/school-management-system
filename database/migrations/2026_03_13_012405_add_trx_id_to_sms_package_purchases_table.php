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
        Schema::table('sms_package_purchases', function (Blueprint $table) {
            // trx_id will store unique reference codes like 'SMS-XYZ12345'
            $table->string('trx_id')->nullable()->after('id')->unique();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sms_package_purchases', function (Blueprint $table) {
            $table->dropColumn('trx_id');
        });
    }
};