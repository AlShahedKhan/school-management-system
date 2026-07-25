<?php

namespace App\Support;

use App\Models\PublicTranslation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class PublicTranslationResolver
{
    public function get(string $key, array $replace = [], ?string $locale = null): string
    {
        $locale ??= app()->getLocale();
        $value = $this->overrides($locale)[$key] ?? null;

        if (is_string($value) && trim($value) !== '') {
            return $this->replacePlaceholders($value, $replace);
        }

        $fallback = __($key, $replace, $locale);

        return $fallback !== $key ? $fallback : $key;
    }

    public function getOrDefault(
        string $key,
        ?string $englishDefault = null,
        ?string $banglaDefault = null,
        array $replace = [],
        ?string $locale = null
    ): string {
        $locale ??= app()->getLocale();
        $value = $this->overrides($locale)[$key] ?? null;

        if (is_string($value) && trim($value) !== '') {
            return $this->replacePlaceholders($value, $replace);
        }

        $fallback = $locale === 'bn'
            ? ($banglaDefault ?: $englishDefault)
            : ($englishDefault ?: $banglaDefault);

        return $this->replacePlaceholders((string) $fallback, $replace);
    }

    public function clearCache(): void
    {
        foreach (config('public.locales', ['en', 'bn']) as $locale) {
            Cache::forget($this->cacheKey($locale));
        }
    }

    private function overrides(string $locale): array
    {
        if (! Schema::hasTable('public_translations')) {
            return [];
        }

        return Cache::rememberForever($this->cacheKey($locale), function () use ($locale) {
            return PublicTranslation::query()
                ->where('is_active', true)
                ->whereNotNull($locale)
                ->pluck($locale, 'key')
                ->map(fn ($value) => is_string($value) ? trim($value) : $value)
                ->filter(fn ($value) => is_string($value) && $value !== '')
                ->all();
        });
    }

    private function cacheKey(string $locale): string
    {
        return "public_translations.{$locale}";
    }

    private function replacePlaceholders(string $value, array $replace): string
    {
        foreach ($replace as $key => $replacement) {
            $value = str_replace(':'.$key, (string) $replacement, $value);
        }

        return $value;
    }
}
