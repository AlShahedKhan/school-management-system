<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateSchoolFeeDiscountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_scope'     => ['required', 'in:all,single,multiple'],
            'student_ids'       => ['nullable', 'array'],
            'student_ids.*'     => 'exists:admission_students,id',
            'class_id'          => 'required|exists:school_classes,id',
            'session_id'        => 'required|exists:school_sessions,id',
            'discount_scope'    => ['required', 'in:session,exam'],
            'fee_type_id'       => ['nullable', 'exists:school_fee_templates,id'],
            'minimum_grade'     => ['nullable', 'string', 'max:20'],
            'discount_type'     => 'required|in:Fixed,Percentage',
            'discount_value'    => 'required|numeric|gt:0',
            'group_id'          => ['required', 'exists:school_groups,id'],
            'section_id'        => ['required', 'exists:school_sections,id'],
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

            $discountScope = $this->discount_scope;
            if (empty($this->fee_type_id)) {
                $validator->errors()->add('fee_type_id', 'A fee type must be selected.');
            }
            if ($discountScope === 'exam' && empty($this->minimum_grade)) {
                $validator->errors()->add('minimum_grade', 'A qualifying grade must be selected.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'student_scope.required'    => 'Student selection is required.',
            'student_scope.in'          => 'Invalid student selection option.',
            'student_ids.*.exists'      => 'Selected student does not exist.',
            'class_id.required'         => 'Class is required.',
            'session_id.required'       => 'Session is required.',
            'discount_scope.required'   => 'Discount scope is required.',
            'discount_scope.in'         => 'Discount scope must be Session or Exam.',
            'fee_type_id.exists'         => 'Selected fee type does not exist.',
            'minimum_grade.string'      => 'Grade must be a valid text value.',
            'discount_type.required'    => 'Discount type is required.',
            'discount_type.in'          => 'Discount type must be Fixed or Percentage.',
            'discount_value.required'   => 'Discount value is required.',
            'discount_value.numeric'    => 'Discount value must be a number.',
            'discount_value.gt'         => 'Discount value must be greater than zero.',
            'group_id.required'         => 'Group is required.',
            'group_id.exists'           => 'Selected group does not exist.',
            'section_id.required'       => 'Section is required.',
            'section_id.exists'         => 'Selected section does not exist.',
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
