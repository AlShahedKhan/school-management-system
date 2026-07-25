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
        Schema::table('dynamic_operations', function (Blueprint $table) {
            $table->string('brand_logo_light')->nullable()->after('brand_logo');
            $table->string('brand_logo_dark')->nullable()->after('brand_logo_light');
            $table->string('brand_favicon')->nullable()->after('brand_logo_dark');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dynamic_operations', function (Blueprint $table) {
            $table->dropColumn(['brand_logo_light', 'brand_logo_dark', 'brand_favicon']);
        });
    }
};
