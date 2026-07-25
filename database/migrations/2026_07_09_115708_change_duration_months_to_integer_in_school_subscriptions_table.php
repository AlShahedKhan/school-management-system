<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     */
    public function up(): void
    {
        Schema::table('school_subscriptions', function (Blueprint $table) {
            $table->integer('duration_months')->change();
            $table->string('status', 255)->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_subscriptions', function (Blueprint $table) {
            $table->enum('duration_months', [2, 4, 12])->change();
            $table->enum('status', ['active', 'expired', 'cancelled'])->default('active')->change();
        });
    }
};
