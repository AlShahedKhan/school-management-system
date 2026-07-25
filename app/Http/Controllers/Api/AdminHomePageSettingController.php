<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\HomePageSettingRequest;
use App\Models\HomePageSetting;
use App\Support\HomePageDefaults;
use App\Support\HomePageContentResolver;

class AdminHomePageSettingController extends Controller
{
    private function ensureSettings(): HomePageSetting
    {
        return HomePageSetting::firstOrCreate(['id' => 1], HomePageDefaults::seedAttributes());
    }

    public function index()
    {
        $this->ensureSettings();

        return response()->json($this->editorPayload());
    }

    public function update(HomePageSettingRequest $request)
    {
        $validated = $request->validated();
        $settings = $this->ensureSettings();
        $validated = $this->mergeMissingHomeDefaults($validated, $settings);

        $settings->fill([
            'hero_title_line_1' => $this->emptyToNull($validated['hero_title_line_1_en'] ?? null),
            'hero_title_line_1_en' => $this->emptyToNull($validated['hero_title_line_1_en'] ?? null),
            'hero_title_line_1_bn' => $this->emptyToNull($validated['hero_title_line_1_bn'] ?? null),
            'hero_highlight_text' => $this->emptyToNull($validated['hero_highlight_text_en'] ?? null),
            'hero_highlight_text_en' => $this->emptyToNull($validated['hero_highlight_text_en'] ?? null),
            'hero_highlight_text_bn' => $this->emptyToNull($validated['hero_highlight_text_bn'] ?? null),
            'hero_title_suffix' => $this->emptyToNull($validated['hero_title_suffix_en'] ?? null),
            'hero_title_suffix_en' => $this->emptyToNull($validated['hero_title_suffix_en'] ?? null),
            'hero_title_suffix_bn' => $this->emptyToNull($validated['hero_title_suffix_bn'] ?? null),
            'hero_accent_color' => $this->emptyToNull($validated['hero_accent_color'] ?? null),
            'hero_accent_soft_color' => $this->emptyToNull($validated['hero_accent_soft_color'] ?? null),
            'hero_primary_button_color' => $this->emptyToNull($validated['hero_primary_button_color'] ?? null),
            'hero_primary_button_hover_color' => $this->emptyToNull($validated['hero_primary_button_hover_color'] ?? null),
            'hero_description' => $this->emptyToNull($validated['hero_description_en'] ?? null),
            'hero_description_en' => $this->emptyToNull($validated['hero_description_en'] ?? null),
            'hero_description_bn' => $this->emptyToNull($validated['hero_description_bn'] ?? null),
            'hero_primary_cta_label' => $this->emptyToNull($validated['hero_primary_cta_label'] ?? null),
            'hero_primary_cta_url' => $this->emptyToNull($validated['hero_primary_cta_url'] ?? null),
            'hero_secondary_cta_label' => $this->emptyToNull($validated['hero_secondary_cta_label'] ?? null),
            'hero_secondary_cta_url' => $this->emptyToNull($validated['hero_secondary_cta_url'] ?? null),
            'hero_dashboard_label' => $this->emptyToNull($validated['hero_dashboard_label'] ?? null),
            'hero_school_name' => $this->emptyToNull($validated['hero_school_name'] ?? null),
            'hero_attendance_label' => $this->emptyToNull($validated['hero_attendance_label'] ?? null),
            'hero_attendance_value' => $this->emptyToNull($validated['hero_attendance_value'] ?? null),
            'hero_activity_title' => $this->emptyToNull($validated['hero_activity_title'] ?? null),
            'hero_status_badge' => $this->emptyToNull($validated['hero_status_badge'] ?? null),
            'hero_status_badge_style' => $validated['hero_status_badge_style'] ?? null,
            'hero_metric_cards' => $validated['hero_metric_cards'],
            'hero_activity_items' => $validated['hero_activity_items'],
            'stats_items' => $validated['stats_items_en'],
            'stats_items_en' => $validated['stats_items_en'],
            'stats_items_bn' => $validated['stats_items_bn'] ?? null,
            'intro_eyebrow' => $this->emptyToNull($validated['intro_eyebrow_en'] ?? null),
            'intro_eyebrow_en' => $this->emptyToNull($validated['intro_eyebrow_en'] ?? null),
            'intro_eyebrow_bn' => $this->emptyToNull($validated['intro_eyebrow_bn'] ?? null),
            'intro_title' => $this->emptyToNull($validated['intro_title_en'] ?? null),
            'intro_title_en' => $this->emptyToNull($validated['intro_title_en'] ?? null),
            'intro_title_bn' => $this->emptyToNull($validated['intro_title_bn'] ?? null),
            'intro_description' => $this->emptyToNull($validated['intro_description_en'] ?? null),
            'intro_description_en' => $this->emptyToNull($validated['intro_description_en'] ?? null),
            'intro_description_bn' => $this->emptyToNull($validated['intro_description_bn'] ?? null),
        ]);
        $settings->save();

        return response()->json($this->editorPayload());
    }

    private function emptyToNull(?string $value): ?string
    {
        return $value === '' ? null : $value;
    }

    private function mergeMissingHomeDefaults(array $validated, HomePageSetting $settings): array
    {
        $defaults = HomePageDefaults::seedAttributes();
        $fields = [
            'hero_title_line_1_en',
            'hero_title_line_1_bn',
            'hero_highlight_text_en',
            'hero_highlight_text_bn',
            'hero_title_suffix_en',
            'hero_title_suffix_bn',
            'hero_accent_color',
            'hero_accent_soft_color',
            'hero_primary_button_color',
            'hero_primary_button_hover_color',
            'hero_description_en',
            'hero_description_bn',
            'hero_primary_cta_label',
            'hero_primary_cta_url',
            'hero_secondary_cta_label',
            'hero_secondary_cta_url',
            'hero_dashboard_label',
            'hero_school_name',
            'hero_attendance_label',
            'hero_attendance_value',
            'hero_activity_title',
            'hero_status_badge',
            'hero_status_badge_style',
            'hero_metric_cards',
            'hero_activity_items',
            'stats_items_en',
            'stats_items_bn',
            'intro_eyebrow_en',
            'intro_eyebrow_bn',
            'intro_title_en',
            'intro_title_bn',
            'intro_description_en',
            'intro_description_bn',
        ];

        foreach ($fields as $field) {
            if (array_key_exists($field, $validated)) {
                continue;
            }

            $validated[$field] = $settings->{$field} ?? $defaults[$field] ?? null;
        }

        $validated['stats_items'] = $validated['stats_items'] ?? $validated['stats_items_en'];

        return $validated;
    }

    private function resolvedPayload(): array
    {
        return app(HomePageContentResolver::class)->resolve();
    }

    private function editorPayload(): array
    {
        return app(HomePageContentResolver::class)->editorPayload();
    }
}
