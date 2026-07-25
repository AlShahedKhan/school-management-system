<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PublicTranslationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'key' => is_string($this->key) ? trim($this->key) : $this->key,
            'group' => is_string($this->group) ? trim($this->group) : $this->group,
            'en' => is_string($this->en) ? trim($this->en) : $this->en,
            'bn' => is_string($this->bn) ? trim($this->bn) : $this->bn,
            'description' => is_string($this->description) ? trim($this->description) : $this->description,
        ]);
    }

    public function rules(): array
    {
        $publicTranslation = $this->route('publicTranslation');
        $keyRules = $publicTranslation
            ? ['prohibited']
            : [
                'required',
                'string',
                'max:150',
                'regex:/^[a-z][a-z0-9_]*(\.[a-z0-9_]+)+$/',
                Rule::unique('public_translations', 'key'),
            ];

        return [
            'key' => $keyRules,
            'group' => ['nullable', 'string', 'max:50'],
            'en' => ['nullable', 'string', 'max:2000'],
            'bn' => ['nullable', 'string', 'max:2000'],
            'description' => ['nullable', 'string', 'max:255'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'key.regex' => 'The key must use dot notation, for example public.nav.home.',
        ];
    }
}
