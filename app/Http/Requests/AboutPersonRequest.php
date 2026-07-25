<?php

namespace App\Http\Requests;

use App\Models\AboutPerson;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AboutPersonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $nameEn = trim((string) $this->input('name_en'));
        $slug = trim((string) $this->input('slug'));

        $this->merge([
            'name_en' => $nameEn,
            'name_bn' => trim((string) $this->input('name_bn')),
            'designation_en' => trim((string) $this->input('designation_en')),
            'designation_bn' => trim((string) $this->input('designation_bn')),
            'summary_en' => trim((string) $this->input('summary_en')),
            'summary_bn' => trim((string) $this->input('summary_bn')),
            'details_en' => trim((string) $this->input('details_en')),
            'details_bn' => trim((string) $this->input('details_bn')),
            'slug' => Str::slug($slug !== '' ? $slug : $nameEn),
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    public function rules(): array
    {
        /** @var AboutPerson|null $aboutPerson */
        $aboutPerson = $this->route('aboutPerson');

        $imageRules = ['image', 'mimes:jpeg,png,jpg,gif,webp', 'max:4096'];

        if ($this->isMethod('post')) {
            array_unshift($imageRules, 'required');
        } else {
            array_unshift($imageRules, 'nullable');
        }

        return [
            'image' => $imageRules,
            'slug' => [
                'required',
                'string',
                'max:160',
                Rule::unique('about_people', 'slug')->ignore($aboutPerson?->id),
            ],
            'name_en' => ['required', 'string', 'max:120'],
            'name_bn' => ['nullable', 'string', 'max:120'],
            'designation_en' => ['required', 'string', 'max:120'],
            'designation_bn' => ['nullable', 'string', 'max:120'],
            'summary_en' => ['required', 'string', 'max:500'],
            'summary_bn' => ['nullable', 'string', 'max:500'],
            'details_en' => ['required', 'string'],
            'details_bn' => ['nullable', 'string'],
            'sort_order' => ['required', 'integer', 'min:1'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
