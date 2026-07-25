<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admission_students', function (Blueprint $table) {
            // Added on 2026-07-06: Add current_country and permanent_country fields
            $table->string('current_country')->nullable()->after('mobile');
            $table->string('permanent_country')->nullable()->after('current_country');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admission_students', function (Blueprint $table) {
            // Added on 2026-07-06: Drop current_country and permanent_country fields
            $table->dropColumn(['current_country', 'permanent_country']);
        });
    }
};
