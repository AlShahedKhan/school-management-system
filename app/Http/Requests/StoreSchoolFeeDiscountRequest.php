<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreSchoolFeeDiscountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_scope'   => ['required', 'in:all,single,multiple'],
            'student_ids'     => ['nullable', 'array'],
            'student_ids.*'   => 'exists:admission_students,id',
            'class_id'        => 'required|exists:school_classes,id',
            'session_id'      => 'required|exists:school_sessions,id',
            'fee_type_id'     => 'required|exists:school_fee_templates,id',
            'fee_name'        => 'required|string|max:255',
            'discount_type'   => 'required|in:Fixed,Percentage',
            'discount_value'  => 'required|numeric|min:0',
            'before_discount' => 'required|numeric',
            'discount_amount' => 'required|numeric',
            'after_discount'  => 'required|numeric',
            'group_id'        => 'nullable',
            'section_id'      => 'nullable',
            'start_date'      => 'nullable|date',
            'end_date'        => 'nullable|date|after_or_equal:start_date',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $scope = $this->student_scope;
            $ids = $this->student_ids ?? [];
            if ($scope === 'single' || $scope === 'multiple') {
                if (empty($ids)) {
                    $validator->errors()->add('student_ids', 'At least one student must be selected.');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'student_scope'          => 'Student selection is required.',
            'student_scope.in'       => 'Invalid student selection option.',
            'student_ids.*.exists'   => 'Selected student does not exist.',
            'end_date.after_or_equal'    => 'The end date must be a date after or equal to the start date.',
            'class_id.required'          => 'Class is required.',
            'session_id.required'        => 'Session is required.',
            'fee_type_id.required'       => 'Fee type is required.',
            'fee_type_id.exists'         => 'Selected fee type does not exist.',
            'fee_name.required'          => 'Fee name is required.',
            'discount_type.required'     => 'Discount type is required.',
            'discount_type.in'           => 'Discount type must be Fixed or Percentage.',
            'discount_value.required'    => 'Discount value is required.',
            'discount_value.numeric'     => 'Discount value must be a number.',
            'before_discount.required'   => 'Before discount amount is required.',
            'discount_amount.required'   => 'Discount amount is required.',
            'after_discount.required'    => 'After discount amount is required.',
            'start_date.date'            => 'Start date must be a valid date.',
            'end_date.date'              => 'End date must be a valid date.',
        ];
    }

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
