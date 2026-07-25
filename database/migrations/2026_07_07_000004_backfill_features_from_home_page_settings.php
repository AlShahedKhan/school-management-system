<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('features') || ! Schema::hasTable('home_page_settings')) {
            return;
        }

        if (DB::table('features')->exists()) {
            return;
        }

        $settings = DB::table('home_page_settings')->where('id', 1)->first();
        if (! $settings || ! isset($settings->feature_items)) {
            return;
        }

        $featureItems = json_decode((string) $settings->feature_items, true);
        if (! is_array($featureItems)) {
            return;
        }

        $rows = collect($featureItems)
            ->filter(fn ($item) => is_array($item))
            ->values()
            ->map(function (array $item, int $index): array {
                $title = trim((string) ($item['title'] ?? ''));
                $description = trim((string) ($item['description'] ?? ''));

                return [
                    'icon' => trim((string) ($item['icon'] ?? 'layout-dashboard')),
                    'title_en' => $title,
                    'title_bn' => $title,
                    'description_en' => $description,
                    'description_bn' => $description,
                    'sort_order' => $index + 1,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })
            ->filter(fn (array $item) => $item['title_en'] !== '' && $item['description_en'] !== '')
            ->all();

        if ($rows !== []) {
            DB::table('features')->insert($rows);
        }
    }

    public function down(): void
    {
        //
    }
};
