<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DashboardNewsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'label' => trim((string) $this->input('label', 'News')),
            'message' => trim((string) $this->input('message')),
            'is_active' => $this->boolean('is_active'),
            'sort_order' => $this->input('sort_order') ?: 1,
        ]);
    }

    public function rules(): array
    {
        return [
            'label' => ['required', 'string', 'max:20'],
            'message' => ['required', 'string', 'max:180'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'sort_order' => ['required', 'integer', 'min:1'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
