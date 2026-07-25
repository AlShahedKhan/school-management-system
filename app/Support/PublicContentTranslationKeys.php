<?php

namespace App\Support;

use App\Models\DynamicOperation;
use App\Models\HomePageSetting;

class PublicContentTranslationKeys
{
    public static function rows(): array
    {
        $dynamic = DynamicOperation::find(1);
        $homeDefaults = HomePageDefaults::seedAttributes();
        $home = HomePageSetting::find(1);

        return array_merge(
            self::brandRows($dynamic),
            self::footerRows($dynamic),
            self::homeRows($home, $homeDefaults)
        );
    }

    private static function brandRows(?DynamicOperation $settings): array
    {
        return [
            self::row(
                'public.brand.title',
                'brand',
                $settings?->brand_title_en ?: $settings?->brand_title ?: 'Astha Academics',
                $settings?->brand_title_bn,
                'Public brand title used in header, footer, and page titles.'
            ),
            self::row(
                'public.brand.description',
                'brand',
                $settings?->brand_description,
                null,
                'Short public brand description.'
            ),
            self::row(
                'public.brand.promotion_text',
                'brand',
                $settings?->promotion_text,
                null,
                'Small public promotion label.'
            ),
        ];
    }

    private static function footerRows(?DynamicOperation $settings): array
    {
        $badges = self::footerBadges($settings);
        $rows = [
            self::row(
                'public.footer.description',
                'footer',
                $settings?->footer_description_en ?: $settings?->footer_description,
                $settings?->footer_description_bn,
                'Public footer description.'
            ),
        ];

        for ($index = 0; $index < 6; $index++) {
            $badge = $badges[$index] ?? [];
            $rows[] = self::row(
                "public.footer.badges.{$index}.label",
                'footer',
                $badge['label_en'] ?? null,
                $badge['label_bn'] ?? null,
                'Footer trust badge label.'
            );
        }

        return $rows;
    }

    private static function homeRows(?HomePageSetting $settings, array $defaults): array
    {
        $rows = [
            self::localizedHomeRow($settings, $defaults, 'hero_title_line_1', 'public.home.hero.title_line_1', 'Hero title first line.'),
            self::localizedHomeRow($settings, $defaults, 'hero_highlight_text', 'public.home.hero.highlight_text', 'Hero highlighted title text.'),
            self::localizedHomeRow($settings, $defaults, 'hero_title_suffix', 'public.home.hero.title_suffix', 'Hero title suffix.'),
            self::localizedHomeRow($settings, $defaults, 'hero_description', 'public.home.hero.description', 'Hero description.'),
            self::simpleHomeRow($settings, $defaults, 'hero_primary_cta_label', 'public.home.hero.primary_cta_label', 'Primary CTA label.'),
            self::simpleHomeRow($settings, $defaults, 'hero_secondary_cta_label', 'public.home.hero.secondary_cta_label', 'Secondary CTA label.'),
            self::simpleHomeRow($settings, $defaults, 'hero_dashboard_label', 'public.home.hero.dashboard_label', 'Hero dashboard label.'),
            self::simpleHomeRow($settings, $defaults, 'hero_school_name', 'public.home.hero.school_name', 'Hero dashboard school name.'),
            self::simpleHomeRow($settings, $defaults, 'hero_attendance_label', 'public.home.hero.attendance_label', 'Hero attendance label.'),
            self::simpleHomeRow($settings, $defaults, 'hero_attendance_value', 'public.home.hero.attendance_value', 'Hero attendance value.'),
            self::simpleHomeRow($settings, $defaults, 'hero_activity_title', 'public.home.hero.activity_title', 'Hero activity title.'),
            self::simpleHomeRow($settings, $defaults, 'hero_status_badge', 'public.home.hero.status_badge', 'Hero status badge text.'),
            self::localizedHomeRow($settings, $defaults, 'intro_eyebrow', 'public.home.intro.eyebrow', 'Intro eyebrow text.'),
            self::localizedHomeRow($settings, $defaults, 'intro_title', 'public.home.intro.title', 'Intro title.'),
            self::localizedHomeRow($settings, $defaults, 'intro_description', 'public.home.intro.description', 'Intro description.'),
        ];

        foreach (self::arrayValue($settings?->stats_items_en, $defaults['stats_items_en']) as $index => $item) {
            $bnItem = self::arrayValue($settings?->stats_items_bn, $defaults['stats_items_bn'])[$index] ?? [];
            $rows[] = self::row("public.home.stats.{$index}.value", 'home', $item['value'] ?? null, $bnItem['value'] ?? null, 'Home stats value.');
            $rows[] = self::row("public.home.stats.{$index}.label", 'home', $item['label'] ?? null, $bnItem['label'] ?? null, 'Home stats label.');
        }

        foreach (self::arrayValue($settings?->hero_metric_cards, $defaults['hero_metric_cards']) as $index => $item) {
            $rows[] = self::row("public.home.hero.metrics.{$index}.label", 'home', $item['label'] ?? null, null, 'Hero metric card label.');
            $rows[] = self::row("public.home.hero.metrics.{$index}.value", 'home', $item['value'] ?? null, null, 'Hero metric card value.');
        }

        foreach (self::arrayValue($settings?->hero_activity_items, $defaults['hero_activity_items']) as $index => $item) {
            $rows[] = self::row("public.home.hero.activities.{$index}.text", 'home', $item['text'] ?? null, null, 'Hero activity text.');
            $rows[] = self::row("public.home.hero.activities.{$index}.meta", 'home', $item['meta'] ?? null, null, 'Hero activity meta text.');
        }

        return $rows;
    }

    private static function localizedHomeRow(?HomePageSetting $settings, array $defaults, string $field, string $key, string $description): array
    {
        return self::row(
            $key,
            'home',
            $settings?->{$field . '_en'} ?: $settings?->{$field} ?: $defaults[$field . '_en'],
            $settings?->{$field . '_bn'} ?: $defaults[$field . '_bn'],
            $description
        );
    }

    private static function simpleHomeRow(?HomePageSetting $settings, array $defaults, string $field, string $key, string $description): array
    {
        return self::row(
            $key,
            'home',
            $settings?->{$field} ?: $defaults[$field],
            null,
            $description
        );
    }

    private static function footerBadges(?DynamicOperation $settings): array
    {
        if (is_array($settings?->footer_trust_badges_i18n)) {
            return $settings->footer_trust_badges_i18n;
        }

        if (is_array($settings?->footer_trust_badges)) {
            return collect($settings->footer_trust_badges)
                ->map(fn (array $badge) => [
                    'label_en' => $badge['label'] ?? null,
                    'label_bn' => null,
                ])
                ->all();
        }

        return [];
    }

    private static function arrayValue(mixed $value, array $fallback): array
    {
        return is_array($value) && $value !== [] ? $value : $fallback;
    }

    private static function row(string $key, string $group, ?string $en, ?string $bn, string $description): array
    {
        return [
            'key' => $key,
            'group' => $group,
            'en' => self::blankToNull($en),
            'bn' => self::blankToNull($bn),
            'description' => $description,
            'is_active' => true,
        ];
    }

    private static function blankToNull(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value !== '' ? $value : null;
    }
}
