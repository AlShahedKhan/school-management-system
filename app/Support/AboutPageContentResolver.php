<?php

namespace App\Support;

use App\Models\AboutPageSetting;
use Illuminate\Support\Facades\Schema;

class AboutPageContentResolver
{
    public function resolve(): array
    {
        $defaults = AboutPageDefaults::seedAttributes();
        $locale = app()->getLocale();

        if (! Schema::hasTable('about_page_settings')) {
            return $defaults;
        }

        $settings = AboutPageSetting::find(1);

        if (! $settings) {
            return $defaults;
        }

        return [
            'page_eyebrow' => $this->localized($locale, $settings->page_eyebrow_en, $settings->page_eyebrow_bn, $defaults['page_eyebrow_en'], $defaults['page_eyebrow_bn']),
            'page_title' => $this->localized($locale, $settings->page_title_en, $settings->page_title_bn, $defaults['page_title_en'], $defaults['page_title_bn']),
            'page_intro' => $this->localized($locale, $settings->page_intro_en, $settings->page_intro_bn, $defaults['page_intro_en'], $defaults['page_intro_bn']),
            'mission_title' => $this->localized($locale, $settings->mission_title_en, $settings->mission_title_bn, $defaults['mission_title_en'], $defaults['mission_title_bn']),
            'mission_summary' => $this->localized($locale, $settings->mission_summary_en, $settings->mission_summary_bn, $defaults['mission_summary_en'], $defaults['mission_summary_bn']),
            'mission_details' => $this->localized($locale, $settings->mission_details_en, $settings->mission_details_bn, $defaults['mission_details_en'], $defaults['mission_details_bn']),
            'vision_title' => $this->localized($locale, $settings->vision_title_en, $settings->vision_title_bn, $defaults['vision_title_en'], $defaults['vision_title_bn']),
            'vision_summary' => $this->localized($locale, $settings->vision_summary_en, $settings->vision_summary_bn, $defaults['vision_summary_en'], $defaults['vision_summary_bn']),
            'vision_details' => $this->localized($locale, $settings->vision_details_en, $settings->vision_details_bn, $defaults['vision_details_en'], $defaults['vision_details_bn']),
        ];
    }

    public function editorPayload(): array
    {
        $defaults = AboutPageDefaults::seedAttributes();

        if (! Schema::hasTable('about_page_settings')) {
            return $defaults;
        }

        $settings = AboutPageSetting::find(1);

        if (! $settings) {
            return $defaults;
        }

        return [
            'page_eyebrow_en' => $this->valueOrDefault($settings->page_eyebrow_en, $defaults['page_eyebrow_en']),
            'page_eyebrow_bn' => $this->valueOrDefault($settings->page_eyebrow_bn, $defaults['page_eyebrow_bn']),
            'page_title_en' => $this->valueOrDefault($settings->page_title_en, $defaults['page_title_en']),
            'page_title_bn' => $this->valueOrDefault($settings->page_title_bn, $defaults['page_title_bn']),
            'page_intro_en' => $this->valueOrDefault($settings->page_intro_en, $defaults['page_intro_en']),
            'page_intro_bn' => $this->valueOrDefault($settings->page_intro_bn, $defaults['page_intro_bn']),
            'mission_title_en' => $this->valueOrDefault($settings->mission_title_en, $defaults['mission_title_en']),
            'mission_title_bn' => $this->valueOrDefault($settings->mission_title_bn, $defaults['mission_title_bn']),
            'mission_summary_en' => $this->valueOrDefault($settings->mission_summary_en, $defaults['mission_summary_en']),
            'mission_summary_bn' => $this->valueOrDefault($settings->mission_summary_bn, $defaults['mission_summary_bn']),
            'mission_details_en' => $this->valueOrDefault($settings->mission_details_en, $defaults['mission_details_en']),
            'mission_details_bn' => $this->valueOrDefault($settings->mission_details_bn, $defaults['mission_details_bn']),
            'vision_title_en' => $this->valueOrDefault($settings->vision_title_en, $defaults['vision_title_en']),
            'vision_title_bn' => $this->valueOrDefault($settings->vision_title_bn, $defaults['vision_title_bn']),
            'vision_summary_en' => $this->valueOrDefault($settings->vision_summary_en, $defaults['vision_summary_en']),
            'vision_summary_bn' => $this->valueOrDefault($settings->vision_summary_bn, $defaults['vision_summary_bn']),
            'vision_details_en' => $this->valueOrDefault($settings->vision_details_en, $defaults['vision_details_en']),
            'vision_details_bn' => $this->valueOrDefault($settings->vision_details_bn, $defaults['vision_details_bn']),
        ];
    }

    private function localized(string $locale, ?string $englishValue, ?string $banglaValue, string $englishDefault, string $banglaDefault): string
    {
        $english = trim((string) $englishValue);
        $bangla = trim((string) $banglaValue);

        if ($locale === 'bn') {
            return $bangla !== ''
                ? $bangla
                : ($english !== '' ? $english : $banglaDefault);
        }

        return $english !== ''
            ? $english
            : $englishDefault;
    }

    private function valueOrDefault(?string $value, string $default): string
    {
        $value = trim((string) $value);

        return $value !== '' ? $value : $default;
    }
}
