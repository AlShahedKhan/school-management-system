<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('home_page_settings', function (Blueprint $table) {
            $table->string('hero_title_line_1_en')->nullable()->after('hero_title_line_1');
            $table->string('hero_title_line_1_bn')->nullable()->after('hero_title_line_1_en');
            $table->string('hero_highlight_text_en')->nullable()->after('hero_highlight_text');
            $table->string('hero_highlight_text_bn')->nullable()->after('hero_highlight_text_en');
            $table->string('hero_title_suffix_en')->nullable()->after('hero_title_suffix');
            $table->string('hero_title_suffix_bn')->nullable()->after('hero_title_suffix_en');
            $table->text('hero_description_en')->nullable()->after('hero_description');
            $table->text('hero_description_bn')->nullable()->after('hero_description_en');
            $table->json('stats_items_en')->nullable()->after('stats_items');
            $table->json('stats_items_bn')->nullable()->after('stats_items_en');
            $table->string('intro_eyebrow_en')->nullable()->after('intro_eyebrow');
            $table->string('intro_eyebrow_bn')->nullable()->after('intro_eyebrow_en');
            $table->string('intro_title_en')->nullable()->after('intro_title');
            $table->string('intro_title_bn')->nullable()->after('intro_title_en');
            $table->text('intro_description_en')->nullable()->after('intro_description');
            $table->text('intro_description_bn')->nullable()->after('intro_description_en');
        });

        DB::table('home_page_settings')->update([
            'hero_title_line_1_en' => DB::raw('hero_title_line_1'),
            'hero_highlight_text_en' => DB::raw('hero_highlight_text'),
            'hero_title_suffix_en' => DB::raw('hero_title_suffix'),
            'hero_description_en' => DB::raw('hero_description'),
            'stats_items_en' => DB::raw('stats_items'),
            'intro_eyebrow_en' => DB::raw('intro_eyebrow'),
            'intro_title_en' => DB::raw('intro_title'),
            'intro_description_en' => DB::raw('intro_description'),
        ]);
    }

    public function down(): void
    {
        Schema::table('home_page_settings', function (Blueprint $table) {
            $table->dropColumn([
                'hero_title_line_1_en',
                'hero_title_line_1_bn',
                'hero_highlight_text_en',
                'hero_highlight_text_bn',
                'hero_title_suffix_en',
                'hero_title_suffix_bn',
                'hero_description_en',
                'hero_description_bn',
                'stats_items_en',
                'stats_items_bn',
                'intro_eyebrow_en',
                'intro_eyebrow_bn',
                'intro_title_en',
                'intro_title_bn',
                'intro_description_en',
                'intro_description_bn',
            ]);
        });
    }
};
