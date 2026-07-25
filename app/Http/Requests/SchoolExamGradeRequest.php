<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SchoolExamGradeRequest extends FormRequest
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
     */
    public function rules(): array
    {
        $isUpdate = $this->route('school_exam_grade') !== null || $this->route('id') !== null;

        if ($isUpdate) {
            return [
                'grade_name'  => ['required', 'string', 'max:255'],
                'grade_point' => ['required', 'numeric'],
                'mark_from'   => ['required', 'numeric'],
                'mark_to'     => ['required', 'numeric'],
                // full_mark is optional during update
                'full_mark'   => ['nullable', 'numeric'],
            ];
        }

        return [
            'full_mark' => ['required', 'numeric'],

            'grades' => ['required', 'array', 'min:1'],

            'grades.*.grade_name' => ['required', 'string', 'max:255'],
            'grades.*.grade_point' => ['required', 'numeric'],
            'grades.*.mark_from' => ['required', 'numeric'],
            'grades.*.mark_to' => ['required', 'numeric'],
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'full_mark.required' => 'Full mark is required.',

            'grades.required' => 'Please add at least one grade row before saving.',
            'grades.min' => 'Please add at least one grade row before saving.',

            'grades.*.grade_name.required' => 'Each grade needs a letter name.',
            'grades.*.grade_point.required' => 'Each grade needs a point value.',
            'grades.*.mark_from.required' => 'Each grade needs a minimum mark.',
            'grades.*.mark_to.required' => 'Each grade needs a maximum mark.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'full_mark' => $this->input('full_mark'),
        ]);
    }
}