<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBlogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image' => ['nullable', 'image', 'max:4096'],
            'status' => ['required', 'in:draft,published'],
            'is_featured' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
            'en.title' => ['required', 'string', 'max:255'],
            'en.description' => ['required', 'string'],
            'en.seo_tags' => ['nullable', 'string'],
            'en.meta_title' => ['nullable', 'string', 'max:255'],
            'en.meta_keywords' => ['nullable', 'string'],
            'en.meta_description' => ['nullable', 'string'],
            'bn.title' => ['required', 'string', 'max:255'],
            'bn.description' => ['required', 'string'],
            'bn.seo_tags' => ['nullable', 'string'],
            'bn.meta_title' => ['nullable', 'string', 'max:255'],
            'bn.meta_keywords' => ['nullable', 'string'],
            'bn.meta_description' => ['nullable', 'string'],
        ];
    }
}
