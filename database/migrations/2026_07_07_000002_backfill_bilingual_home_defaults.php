<?php

use App\Support\HomePageDefaults;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $hero = HomePageDefaults::heroContentDefaults();
        $stats = HomePageDefaults::statsContentDefaults();
        $intro = HomePageDefaults::introContentDefaults();

        DB::table('home_page_settings')
            ->whereNull('hero_title_line_1_bn')
            ->update(['hero_title_line_1_bn' => $hero['bn']['title_line_1']]);

        DB::table('home_page_settings')
            ->whereNull('hero_highlight_text_bn')
            ->update(['hero_highlight_text_bn' => $hero['bn']['highlight_text']]);

        DB::table('home_page_settings')
            ->whereNull('hero_title_suffix_bn')
            ->update(['hero_title_suffix_bn' => $hero['bn']['title_suffix']]);

        DB::table('home_page_settings')
            ->whereNull('hero_description_bn')
            ->update(['hero_description_bn' => $hero['bn']['description']]);

        DB::table('home_page_settings')
            ->whereNull('stats_items_bn')
            ->update(['stats_items_bn' => json_encode($stats['bn'], JSON_UNESCAPED_UNICODE)]);

        DB::table('home_page_settings')
            ->whereNull('intro_eyebrow_bn')
            ->update(['intro_eyebrow_bn' => $intro['bn']['eyebrow']]);

        DB::table('home_page_settings')
            ->whereNull('intro_title_bn')
            ->update(['intro_title_bn' => $intro['bn']['title']]);

        DB::table('home_page_settings')
            ->whereNull('intro_description_bn')
            ->update(['intro_description_bn' => $intro['bn']['description']]);
    }

    public function down(): void
    {
        //
    }
};
