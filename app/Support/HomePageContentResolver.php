<?php

namespace App\Support;

use App\Models\HomePageSetting;
use Illuminate\Support\Facades\Schema;

class HomePageContentResolver
{
    public function resolve(): array
    {
        $defaults = HomePageDefaults::seedAttributes();
        $locale = app()->getLocale();

        if (!Schema::hasTable('home_page_settings')) {
            return $defaults;
        }

        $settings = HomePageSetting::find(1);
        if (!$settings) {
            return $defaults;
        }

        $payload = [
            'hero_title_line_1' => $this->resolveLocalizedText(
                $locale,
                $settings->hero_title_line_1_en,
                $settings->hero_title_line_1_bn,
                $settings->hero_title_line_1,
                $defaults['hero_title_line_1_en'],
                $defaults['hero_title_line_1_bn']
            ),
            'hero_highlight_text' => $this->resolveLocalizedText(
                $locale,
                $settings->hero_highlight_text_en,
                $settings->hero_highlight_text_bn,
                $settings->hero_highlight_text,
                $defaults['hero_highlight_text_en'],
                $defaults['hero_highlight_text_bn']
            ),
            'hero_title_suffix' => $this->resolveLocalizedText(
                $locale,
                $settings->hero_title_suffix_en,
                $settings->hero_title_suffix_bn,
                $settings->hero_title_suffix,
                $defaults['hero_title_suffix_en'],
                $defaults['hero_title_suffix_bn']
            ),
            'hero_accent_color' => $this->colorOrDefault($settings->hero_accent_color, $defaults['hero_accent_color']),
            'hero_accent_soft_color' => $this->colorOrDefault($settings->hero_accent_soft_color, $defaults['hero_accent_soft_color']),
            'hero_primary_button_color' => $this->colorOrDefault($settings->hero_primary_button_color, $defaults['hero_primary_button_color']),
            'hero_primary_button_hover_color' => $this->colorOrDefault($settings->hero_primary_button_hover_color, $defaults['hero_primary_button_hover_color']),
            'hero_description' => $this->resolveLocalizedText(
                $locale,
                $settings->hero_description_en,
                $settings->hero_description_bn,
                $settings->hero_description,
                $defaults['hero_description_en'],
                $defaults['hero_description_bn']
            ),
            'hero_primary_cta_label' => $this->textOrDefault($settings->hero_primary_cta_label, $defaults['hero_primary_cta_label']),
            'hero_primary_cta_url' => $this->urlOrDefault($settings->hero_primary_cta_url, $defaults['hero_primary_cta_url']),
            'hero_secondary_cta_label' => $this->textOrDefault($settings->hero_secondary_cta_label, $defaults['hero_secondary_cta_label']),
            'hero_secondary_cta_url' => $this->urlOrDefault($settings->hero_secondary_cta_url, $defaults['hero_secondary_cta_url']),
            'hero_dashboard_label' => $this->textOrDefault($settings->hero_dashboard_label, $defaults['hero_dashboard_label']),
            'hero_school_name' => $this->textOrDefault($settings->hero_school_name, $defaults['hero_school_name']),
            'hero_attendance_label' => $this->textOrDefault($settings->hero_attendance_label, $defaults['hero_attendance_label']),
            'hero_attendance_value' => $this->textOrDefault($settings->hero_attendance_value, $defaults['hero_attendance_value']),
            'hero_activity_title' => $this->textOrDefault($settings->hero_activity_title, $defaults['hero_activity_title']),
            'hero_status_badge' => $this->textOrDefault($settings->hero_status_badge, $defaults['hero_status_badge']),
            'hero_status_badge_style' => in_array($settings->hero_status_badge_style, HomePageDefaults::BADGE_STYLES, true)
                ? $settings->hero_status_badge_style
                : $defaults['hero_status_badge_style'],
            'hero_metric_cards' => $this->normalizeMetricCards($settings->hero_metric_cards, $defaults['hero_metric_cards']),
            'hero_activity_items' => $this->normalizeActivityItems($settings->hero_activity_items, $defaults['hero_activity_items']),
            'stats_items' => $this->resolveLocalizedStatsItems(
                $locale,
                $settings->stats_items_en,
                $settings->stats_items_bn,
                $settings->stats_items,
                $defaults['stats_items_en'],
                $defaults['stats_items_bn']
            ),
            'intro_eyebrow' => $this->resolveLocalizedText(
                $locale,
                $settings->intro_eyebrow_en,
                $settings->intro_eyebrow_bn,
                $settings->intro_eyebrow,
                $defaults['intro_eyebrow_en'],
                $defaults['intro_eyebrow_bn']
            ),
            'intro_title' => $this->resolveLocalizedText(
                $locale,
                $settings->intro_title_en,
                $settings->intro_title_bn,
                $settings->intro_title,
                $defaults['intro_title_en'],
                $defaults['intro_title_bn']
            ),
            'intro_description' => $this->resolveLocalizedText(
                $locale,
                $settings->intro_description_en,
                $settings->intro_description_bn,
                $settings->intro_description,
                $defaults['intro_description_en'],
                $defaults['intro_description_bn']
            ),
        ];

        return $this->withPublicTranslations($payload);
    }

    public function editorPayload(): array
    {
        $defaults = HomePageDefaults::seedAttributes();

        if (!Schema::hasTable('home_page_settings')) {
            return $defaults;
        }

        $settings = HomePageSetting::find(1);
        if (!$settings) {
            return $defaults;
        }

        return array_merge($this->resolve(), [
            'hero_title_line_1_en' => $this->textOrDefault($settings->hero_title_line_1_en ?: $settings->hero_title_line_1, $defaults['hero_title_line_1_en']),
            'hero_title_line_1_bn' => $this->textOrDefault($settings->hero_title_line_1_bn, $defaults['hero_title_line_1_bn']),
            'hero_highlight_text_en' => $this->textOrDefault($settings->hero_highlight_text_en ?: $settings->hero_highlight_text, $defaults['hero_highlight_text_en']),
            'hero_highlight_text_bn' => $this->textOrDefault($settings->hero_highlight_text_bn, $defaults['hero_highlight_text_bn']),
            'hero_title_suffix_en' => $this->textOrDefault($settings->hero_title_suffix_en ?: $settings->hero_title_suffix, $defaults['hero_title_suffix_en']),
            'hero_title_suffix_bn' => $this->textOrDefault($settings->hero_title_suffix_bn, $defaults['hero_title_suffix_bn']),
            'hero_description_en' => $this->textOrDefault($settings->hero_description_en ?: $settings->hero_description, $defaults['hero_description_en']),
            'hero_description_bn' => $this->textOrDefault($settings->hero_description_bn, $defaults['hero_description_bn']),
            'stats_items_en' => $this->normalizeStatsItems($settings->stats_items_en ?: $settings->stats_items, $defaults['stats_items_en']),
            'stats_items_bn' => $this->normalizeStatsItems($settings->stats_items_bn, $defaults['stats_items_bn']),
            'intro_eyebrow_en' => $this->textOrDefault($settings->intro_eyebrow_en ?: $settings->intro_eyebrow, $defaults['intro_eyebrow_en']),
            'intro_eyebrow_bn' => $this->textOrDefault($settings->intro_eyebrow_bn, $defaults['intro_eyebrow_bn']),
            'intro_title_en' => $this->textOrDefault($settings->intro_title_en ?: $settings->intro_title, $defaults['intro_title_en']),
            'intro_title_bn' => $this->textOrDefault($settings->intro_title_bn, $defaults['intro_title_bn']),
            'intro_description_en' => $this->textOrDefault($settings->intro_description_en ?: $settings->intro_description, $defaults['intro_description_en']),
            'intro_description_bn' => $this->textOrDefault($settings->intro_description_bn, $defaults['intro_description_bn']),
        ]);
    }

    private function textOrDefault(?string $value, string $default): string
    {
        $value = trim((string) $value);
        return $value !== '' ? $value : $default;
    }

    private function resolveLocalizedText(
        string $locale,
        ?string $englishValue,
        ?string $banglaValue,
        ?string $legacyValue,
        string $englishDefault,
        string $banglaDefault
    ): string {
        $english = trim((string) $englishValue);
        $bangla = trim((string) $banglaValue);
        $legacy = trim((string) $legacyValue);

        if ($locale === 'bn') {
            return $bangla !== ''
                ? $bangla
                : ($english !== '' ? $english : ($legacy !== '' ? $legacy : $banglaDefault));
        }

        return $english !== ''
            ? $english
            : ($legacy !== '' ? $legacy : $englishDefault);
    }

    private function urlOrDefault(?string $value, string $default): string
    {
        $value = trim((string) $value);
        return $value !== '' ? $value : $default;
    }

    private function colorOrDefault(?string $value, string $default): string
    {
        $value = trim((string) $value);

        return preg_match('/^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $value)
            ? strtoupper($value)
            : $default;
    }

    private function normalizeMetricCards(mixed $items, array $defaults): array
    {
        return $this->normalizeRepeatable(
            $items,
            $defaults,
            fn (array $item) => isset($item['label'], $item['value'], $item['tone'])
                && trim((string) $item['label']) !== ''
                && trim((string) $item['value']) !== ''
                && in_array($item['tone'], HomePageDefaults::TONES, true),
            3,
            3
        );
    }

    private function normalizeActivityItems(mixed $items, array $defaults): array
    {
        return $this->normalizeRepeatable(
            $items,
            $defaults,
            fn (array $item) => isset($item['text'], $item['meta'], $item['tone'])
                && trim((string) $item['text']) !== ''
                && trim((string) $item['meta']) !== ''
                && in_array($item['tone'], HomePageDefaults::TONES, true),
            1,
            4
        );
    }

    private function normalizeStatsItems(mixed $items, array $defaults): array
    {
        return $this->normalizeRepeatable(
            $items,
            $defaults,
            fn (array $item) => isset($item['label'], $item['value'])
                && trim((string) $item['label']) !== ''
                && trim((string) $item['value']) !== '',
            4,
            4
        );
    }

    private function resolveLocalizedStatsItems(
        string $locale,
        mixed $englishItems,
        mixed $banglaItems,
        mixed $legacyItems,
        array $englishDefaults,
        array $banglaDefaults
    ): array {
        $english = $this->normalizeStatsItems($englishItems, $englishDefaults);
        $legacy = $this->normalizeStatsItems($legacyItems, $englishDefaults);

        if ($locale !== 'bn') {
            return is_array($englishItems) ? $english : $legacy;
        }

        if (is_array($banglaItems)) {
            $bangla = $this->normalizeStatsItems($banglaItems, $banglaDefaults);

            if ($bangla !== $banglaDefaults || $banglaItems === $banglaDefaults) {
                return $bangla;
            }
        }

        if (is_array($englishItems)) {
            return $english;
        }

        return $legacy;
    }

    private function normalizeRepeatable(mixed $items, array $defaults, callable $validator, int $min, int $max): array
    {
        if (!is_array($items)) {
            return $defaults;
        }

        $normalized = collect($items)
            ->filter(fn ($item) => is_array($item) && $validator($item))
            ->take($max)
            ->map(fn (array $item) => array_map(
                fn ($value) => is_string($value) ? trim($value) : $value,
                $item
            ))
            ->values()
            ->all();

        return count($normalized) >= $min ? $normalized : $defaults;
    }

    private function withPublicTranslations(array $payload): array
    {
        $fieldKeys = [
            'hero_title_line_1' => 'public.home.hero.title_line_1',
            'hero_highlight_text' => 'public.home.hero.highlight_text',
            'hero_title_suffix' => 'public.home.hero.title_suffix',
            'hero_description' => 'public.home.hero.description',
            'hero_primary_cta_label' => 'public.home.hero.primary_cta_label',
            'hero_secondary_cta_label' => 'public.home.hero.secondary_cta_label',
            'hero_dashboard_label' => 'public.home.hero.dashboard_label',
            'hero_school_name' => 'public.home.hero.school_name',
            'hero_attendance_label' => 'public.home.hero.attendance_label',
            'hero_attendance_value' => 'public.home.hero.attendance_value',
            'hero_activity_title' => 'public.home.hero.activity_title',
            'hero_status_badge' => 'public.home.hero.status_badge',
            'intro_eyebrow' => 'public.home.intro.eyebrow',
            'intro_title' => 'public.home.intro.title',
            'intro_description' => 'public.home.intro.description',
        ];

        foreach ($fieldKeys as $field => $key) {
            $payload[$field] = $this->translatedText($key, $payload[$field] ?? '');
        }

        $payload['stats_items'] = collect($payload['stats_items'] ?? [])
            ->map(fn (array $item, int $index) => [
                'value' => $this->translatedText("public.home.stats.{$index}.value", $item['value'] ?? ''),
                'label' => $this->translatedText("public.home.stats.{$index}.label", $item['label'] ?? ''),
            ])
            ->values()
            ->all();

        $payload['hero_metric_cards'] = collect($payload['hero_metric_cards'] ?? [])
            ->map(fn (array $item, int $index) => [
                ...$item,
                'label' => $this->translatedText("public.home.hero.metrics.{$index}.label", $item['label'] ?? ''),
                'value' => $this->translatedText("public.home.hero.metrics.{$index}.value", $item['value'] ?? ''),
            ])
            ->values()
            ->all();

        $payload['hero_activity_items'] = collect($payload['hero_activity_items'] ?? [])
            ->map(fn (array $item, int $index) => [
                ...$item,
                'text' => $this->translatedText("public.home.hero.activities.{$index}.text", $item['text'] ?? ''),
                'meta' => $this->translatedText("public.home.hero.activities.{$index}.meta", $item['meta'] ?? ''),
            ])
            ->values()
            ->all();

        return $payload;
    }

    private function translatedText(string $key, string $fallback): string
    {
        return app(PublicTranslationResolver::class)->getOrDefault($key, $fallback, $fallback);
    }
}
