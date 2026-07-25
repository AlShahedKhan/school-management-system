<?php

namespace App\Http\Requests;

use App\Support\HomePageDefaults;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FeatureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'title_en' => trim((string) $this->input('title_en')),
            'title_bn' => trim((string) $this->input('title_bn')),
            'description_en' => trim((string) $this->input('description_en')),
            'description_bn' => trim((string) $this->input('description_bn')),
            'icon' => trim((string) $this->input('icon')),
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    public function rules(): array
    {
        return [
            'icon' => ['required', Rule::in(HomePageDefaults::ICONS)],
            'title_en' => ['required', 'string', 'max:60'],
            'title_bn' => ['required', 'string', 'max:60'],
            'description_en' => ['required', 'string'],
            'description_bn' => ['required', 'string'],
            'sort_order' => ['required', 'integer', 'min:1'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
