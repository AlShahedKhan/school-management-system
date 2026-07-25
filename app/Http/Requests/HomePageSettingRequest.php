<?php

namespace App\Http\Requests;

use App\Support\HomePageDefaults;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HomePageSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge($this->trimPayload($this->all()));
    }

    public function rules(): array
    {
        return [
            'hero_title_line_1' => ['nullable', 'string', 'max:120'],
            'hero_title_line_1_en' => ['nullable', 'string', 'max:120'],
            'hero_title_line_1_bn' => ['nullable', 'string', 'max:120'],
            'hero_highlight_text' => ['nullable', 'string', 'max:80'],
            'hero_highlight_text_en' => ['nullable', 'string', 'max:80'],
            'hero_highlight_text_bn' => ['nullable', 'string', 'max:80'],
            'hero_title_suffix' => ['nullable', 'string', 'max:160'],
            'hero_title_suffix_en' => ['nullable', 'string', 'max:160'],
            'hero_title_suffix_bn' => ['nullable', 'string', 'max:160'],
            'hero_accent_color' => ['nullable', 'regex:/^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/'],
            'hero_accent_soft_color' => ['nullable', 'regex:/^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/'],
            'hero_primary_button_color' => ['nullable', 'regex:/^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/'],
            'hero_primary_button_hover_color' => ['nullable', 'regex:/^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/'],
            'hero_description' => ['nullable', 'string', 'max:320'],
            'hero_description_en' => ['nullable', 'string', 'max:320'],
            'hero_description_bn' => ['nullable', 'string', 'max:320'],
            'hero_primary_cta_label' => ['nullable', 'string', 'max:30'],
            'hero_primary_cta_url' => ['nullable', 'string', 'max:255'],
            'hero_secondary_cta_label' => ['nullable', 'string', 'max:30'],
            'hero_secondary_cta_url' => ['nullable', 'string', 'max:255'],
            'hero_dashboard_label' => ['nullable', 'string', 'max:40'],
            'hero_school_name' => ['nullable', 'string', 'max:120'],
            'hero_attendance_label' => ['nullable', 'string', 'max:60'],
            'hero_attendance_value' => ['nullable', 'string', 'max:20'],
            'hero_activity_title' => ['nullable', 'string', 'max:60'],
            'hero_status_badge' => ['nullable', 'string', 'max:40'],
            'hero_status_badge_style' => ['nullable', Rule::in(HomePageDefaults::BADGE_STYLES)],
            'hero_metric_cards' => ['nullable', 'array', 'size:3'],
            'hero_metric_cards.*.label' => ['nullable', 'string', 'max:40'],
            'hero_metric_cards.*.value' => ['nullable', 'string', 'max:20'],
            'hero_metric_cards.*.tone' => ['required', Rule::in(HomePageDefaults::TONES)],
            'hero_activity_items' => ['nullable', 'array', 'min:1', 'max:4'],
            'hero_activity_items.*.text' => ['nullable', 'string', 'max:120'],
            'hero_activity_items.*.meta' => ['nullable', 'string', 'max:30'],
            'hero_activity_items.*.tone' => ['required', Rule::in(HomePageDefaults::TONES)],
            'stats_items' => ['nullable', 'array', 'size:4'],
            'stats_items_en' => ['nullable', 'array', 'size:4'],
            'stats_items_bn' => ['nullable', 'array', 'size:4'],
            'stats_items.*.value' => ['nullable', 'string', 'max:20'],
            'stats_items.*.label' => ['nullable', 'string', 'max:50'],
            'stats_items_en.*.value' => ['nullable', 'string', 'max:20'],
            'stats_items_en.*.label' => ['nullable', 'string', 'max:50'],
            'stats_items_bn.*.value' => ['nullable', 'string', 'max:20'],
            'stats_items_bn.*.label' => ['nullable', 'string', 'max:50'],
            'intro_eyebrow' => ['nullable', 'string', 'max:50'],
            'intro_eyebrow_en' => ['nullable', 'string', 'max:50'],
            'intro_eyebrow_bn' => ['nullable', 'string', 'max:50'],
            'intro_title' => ['nullable', 'string', 'max:160'],
            'intro_title_en' => ['nullable', 'string', 'max:160'],
            'intro_title_bn' => ['nullable', 'string', 'max:160'],
            'intro_description' => ['nullable', 'string', 'max:260'],
            'intro_description_en' => ['nullable', 'string', 'max:260'],
            'intro_description_bn' => ['nullable', 'string', 'max:260'],
        ];
    }

    public function messages(): array
    {
        return [
            'hero_metric_cards.size' => 'Hero metric cards must contain exactly 3 items.',
            'hero_activity_items.min' => 'At least 1 hero activity item is required.',
            'hero_activity_items.max' => 'Hero activity items cannot exceed 4 items.',
            'stats_items.size' => 'Stats items must contain exactly 4 items.',
            'stats_items_en.size' => 'English stats items must contain exactly 4 items.',
            'stats_items_bn.size' => 'Bangla stats items must contain exactly 4 items.',
            'hero_accent_color.regex' => 'Hero accent color must be a valid hex color.',
            'hero_accent_soft_color.regex' => 'Hero soft accent color must be a valid hex color.',
            'hero_primary_button_color.regex' => 'Primary button color must be a valid hex color.',
            'hero_primary_button_hover_color.regex' => 'Primary button hover color must be a valid hex color.',
        ];
    }

    private function trimPayload(mixed $value): mixed
    {
        if (is_array($value)) {
            return array_map(fn ($item) => $this->trimPayload($item), $value);
        }

        return is_string($value) ? trim($value) : $value;
    }
}
