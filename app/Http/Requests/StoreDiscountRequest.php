<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreDiscountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'class_id'                => 'required|exists:school_classes,id',
            'group_id'                => 'nullable|exists:school_groups,id',
            'section_id'              => 'nullable|exists:school_sections,id',
            'session_id'              => 'required|exists:school_sessions,id',
            'discount_category'       => 'required|in:exam_waiver,specific_months,full_session',
            'discount_type'           => 'required|in:Fixed,Percentage',
            'discount_value'          => 'required|numeric|min:0',
            'fee_template_id'         => 'required|exists:school_fee_templates,id',
            'student_scope'           => 'required|in:selected,all',
            'student_ids'             => 'required_if:student_scope,selected|array',
            'student_ids.*'           => 'exists:admission_students,id',
            'auto_apply_new_students' => 'boolean',
            'exam_id'                 => 'nullable|exists:school_exam_names,id',
            'min_gpa'                 => 'nullable|numeric|min:0|max:5',
            'min_marks'               => 'nullable|numeric|min:0',
            'months'                  => 'nullable|array',
            'months.*'                => 'string',
        ];

        // Percentage must be between 0 and 100
        if ($this->input('discount_type') === 'Percentage') {
            $rules['discount_value'] = 'required|numeric|min:0|max:100';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'class_id.required'           => 'Class is required.',
            'class_id.exists'             => 'Selected class does not exist.',
            'session_id.required'         => 'Session is required.',
            'session_id.exists'           => 'Selected session does not exist.',
            'discount_category.required'  => 'Discount category is required.',
            'discount_category.in'        => 'Discount category must be exam_waiver, specific_months, or full_session.',
            'discount_type.required'      => 'Discount type is required.',
            'discount_type.in'            => 'Discount type must be Fixed or Percentage.',
            'discount_value.required'     => 'Discount value is required.',
            'discount_value.numeric'      => 'Discount value must be a number.',
            'discount_value.min'          => 'Discount value cannot be negative.',
            'discount_value.max'          => 'Percentage discount cannot exceed 100%.',
            'fee_template_id.required'    => 'Fee type is required.',
            'fee_template_id.exists'      => 'Selected fee type does not exist.',
            'student_scope.required'      => 'Student scope is required.',
            'student_scope.in'            => 'Student scope must be selected or all.',
            'student_ids.required_if'     => 'Please select at least one student.',
            'student_ids.array'           => 'Student IDs must be an array.',
            'end_date.after_or_equal'     => 'End date must be on or after start date.',
        ];
    }

    /**
     * Return JSON error response for API requests.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'status'  => 'error',
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422)
        );
    }
}
