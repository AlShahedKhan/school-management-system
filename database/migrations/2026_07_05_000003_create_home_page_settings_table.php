<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_page_settings', function (Blueprint $table) {
            $table->id();
            $table->string('hero_title_line_1')->nullable();
            $table->string('hero_highlight_text')->nullable();
            $table->string('hero_title_suffix')->nullable();
            $table->text('hero_description')->nullable();
            $table->string('hero_primary_cta_label')->nullable();
            $table->string('hero_primary_cta_url')->nullable();
            $table->string('hero_secondary_cta_label')->nullable();
            $table->string('hero_secondary_cta_url')->nullable();
            $table->string('hero_dashboard_label')->nullable();
            $table->string('hero_school_name')->nullable();
            $table->string('hero_attendance_label')->nullable();
            $table->string('hero_attendance_value')->nullable();
            $table->string('hero_status_badge')->nullable();
            $table->string('hero_status_badge_style')->nullable();
            $table->json('hero_metric_cards')->nullable();
            $table->json('hero_activity_items')->nullable();
            $table->json('stats_items')->nullable();
            $table->string('intro_eyebrow')->nullable();
            $table->string('intro_title')->nullable();
            $table->text('intro_description')->nullable();
            $table->json('feature_items')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_page_settings');
    }
};
