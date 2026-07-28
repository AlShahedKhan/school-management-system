<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateSchoolFeeDiscountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $schoolId = $this->user() ? \App\Models\School::where('user_id', $this->user()->id)->first()?->id : null;

        return [
            'student_id' => [
                'required',
                'exists:admission_students,id',
                Rule::unique('school_fee_discounts')->where(function ($query) use ($schoolId) {
                    return $query->where('fee_type_id', $this->fee_type_id)
                        ->where('session_id', $this->session_id)
                        ->where('school_id', $schoolId)
                        ->where('student_id', $this->student_id);
                })->ignore($this->route('fee_discount'))
            ],
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

    public function messages(): array
    {
        return [
            'student_id.unique'              => 'This student already has a discount for this fee type in this session.',
            'end_date.after_or_equal'        => 'The end date must be a date after or equal to the start date.',
            'class_id.required'              => 'Class is required.',
            'session_id.required'            => 'Session is required.',
            'fee_type_id.required'           => 'Fee type is required.',
            'fee_type_id.exists'             => 'Selected fee type does not exist.',
            'fee_name.required'              => 'Fee name is required.',
            'discount_type.required'         => 'Discount type is required.',
            'discount_type.in'               => 'Discount type must be Fixed or Percentage.',
            'discount_value.required'        => 'Discount value is required.',
            'discount_value.numeric'         => 'Discount value must be a number.',
            'before_discount.required'       => 'Before discount amount is required.',
            'discount_amount.required'       => 'Discount amount is required.',
            'after_discount.required'        => 'After discount amount is required.',
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
