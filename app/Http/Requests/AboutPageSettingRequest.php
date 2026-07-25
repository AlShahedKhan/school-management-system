<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AboutPageSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    protected function prepareForValidation(): void
    {
        $fields = [
            'page_eyebrow_en',
            'page_eyebrow_bn',
            'page_title_en',
            'page_title_bn',
            'page_intro_en',
            'page_intro_bn',
            'mission_title_en',
            'mission_title_bn',
            'mission_summary_en',
            'mission_summary_bn',
            'mission_details_en',
            'mission_details_bn',
            'vision_title_en',
            'vision_title_bn',
            'vision_summary_en',
            'vision_summary_bn',
            'vision_details_en',
            'vision_details_bn',
        ];

        $normalized = [];

        foreach ($fields as $field) {
            $normalized[$field] = trim((string) $this->input($field));
        }

        $this->merge($normalized);
    }

    public function rules(): array
    {
        return [
            'page_eyebrow_en' => ['required', 'string', 'max:120'],
            'page_eyebrow_bn' => ['nullable', 'string', 'max:120'],
            'page_title_en' => ['required', 'string', 'max:180'],
            'page_title_bn' => ['nullable', 'string', 'max:180'],
            'page_intro_en' => ['required', 'string', 'max:1200'],
            'page_intro_bn' => ['nullable', 'string', 'max:1200'],
            'mission_title_en' => ['required', 'string', 'max:120'],
            'mission_title_bn' => ['nullable', 'string', 'max:120'],
            'mission_summary_en' => ['required', 'string', 'max:1200'],
            'mission_summary_bn' => ['nullable', 'string', 'max:1200'],
            'mission_details_en' => ['required', 'string'],
            'mission_details_bn' => ['nullable', 'string'],
            'vision_title_en' => ['required', 'string', 'max:120'],
            'vision_title_bn' => ['nullable', 'string', 'max:120'],
            'vision_summary_en' => ['required', 'string', 'max:1200'],
            'vision_summary_bn' => ['nullable', 'string', 'max:1200'],
            'vision_details_en' => ['required', 'string'],
            'vision_details_bn' => ['nullable', 'string'],
        ];
    }
}
