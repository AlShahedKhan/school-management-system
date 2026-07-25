<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('home_page_settings', function (Blueprint $table) {
            $table->string('hero_accent_color')->nullable()->after('hero_title_suffix');
            $table->string('hero_accent_soft_color')->nullable()->after('hero_accent_color');
            $table->string('hero_primary_button_color')->nullable()->after('hero_accent_soft_color');
            $table->string('hero_primary_button_hover_color')->nullable()->after('hero_primary_button_color');
        });
    }

    public function down(): void
    {
        Schema::table('home_page_settings', function (Blueprint $table) {
            $table->dropColumn([
                'hero_accent_color',
                'hero_accent_soft_color',
                'hero_primary_button_color',
                'hero_primary_button_hover_color',
            ]);
        });
    }
};
