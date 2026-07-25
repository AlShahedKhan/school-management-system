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
            // Adds status tracking for the manual approval workflow
            $table->string('status')->default('pending')->after('available_sms');
            $table->string('payment_method')->nullable()->after('status');
            $table->text('admin_note')->nullable()->after('payment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sms_package_purchases', function (Blueprint $table) {
            $table->dropColumn(['status', 'payment_method', 'admin_note']);
        });
    }
};