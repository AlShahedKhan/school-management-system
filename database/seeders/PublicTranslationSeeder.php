<?php

namespace Database\Seeders;

use App\Models\PublicTranslation;
use App\Support\PublicContentTranslationKeys;
use App\Support\PublicTranslationResolver;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class PublicTranslationSeeder extends Seeder
{
    public function run(): void
    {
        $english = Arr::dot(require lang_path('en/public.php'));
        $bangla = Arr::dot(require lang_path('bn/public.php'));
        $keys = collect(array_keys($english))
            ->merge(array_keys($bangla))
            ->unique()
            ->sort()
            ->values();

        foreach ($keys as $key) {
            $fullKey = "public.{$key}";

            PublicTranslation::updateOrCreate(
                ['key' => $fullKey],
                [
                    'group' => explode('.', $key)[0] ?? null,
                    'en' => $english[$key] ?? null,
                    'bn' => $bangla[$key] ?? null,
                    'description' => null,
                    'is_active' => true,
                ]
            );
        }

        foreach (PublicContentTranslationKeys::rows() as $row) {
            PublicTranslation::updateOrCreate(
                ['key' => $row['key']],
                [
                    'group' => $row['group'],
                    'en' => $row['en'],
                    'bn' => $row['bn'],
                    'description' => $row['description'],
                    'is_active' => $row['is_active'],
                ]
            );
        }

        app(PublicTranslationResolver::class)->clearCache();
    }
}
