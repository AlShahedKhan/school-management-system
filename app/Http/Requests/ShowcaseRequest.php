<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ShowcaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $imageRules = ['image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:4096'];

        if ($this->isMethod('post')) {
            array_unshift($imageRules, 'required');
        } else {
            array_unshift($imageRules, 'nullable');
        }

        return [
            'title_en' => ['required', 'string', 'max:255'],
            'title_bn' => ['nullable', 'string', 'max:255'],
            'image' => $imageRules,
        ];
    }
}
