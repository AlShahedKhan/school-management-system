<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateFeeTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'class_id'      => ['required', 'exists:school_classes,id'],
            'session_id'    => ['required', 'exists:school_sessions,id'],
            'fee_type_name' => ['required', 'string'],
            'fee_name'      => ['required', 'string', 'max:255'],
            'amount'        => ['required', 'numeric', 'min:0'],
            'pay_date'      => ['nullable', 'date'],
            'group_id'      => ['nullable'],
            'section_id'    => ['nullable'],
            'exam_id'       => ['nullable', 'exists:school_exam_names,id'],
            'description'   => ['nullable', 'string'],
            'frequency'     => ['nullable', 'in:one_time,monthly,per_exam,event_triggered'],
            'due_day'       => ['nullable', 'integer', 'min:1', 'max:31'],
            'food_type'     => ['nullable', 'in:single,multiple,all'],
            'student_id'    => ['nullable', 'exists:admission_students,id'],
            'student_ids'   => ['nullable', 'array'],
            'student_ids.*' => ['exists:admission_students,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'class_id.required'      => 'Please select a class.',
            'class_id.exists'        => 'The selected class is invalid.',
            'session_id.required'    => 'Please select a session.',
            'session_id.exists'      => 'The selected session is invalid.',
            'fee_type_name.required' => 'Please select a fee type.',
            'fee_name.required'      => 'Fee name is required.',
            'fee_name.max'           => 'Fee name must not exceed 255 characters.',
            'amount.required'        => 'Amount is required.',
            'amount.numeric'         => 'Amount must be a valid number.',
            'amount.min'             => 'Amount must be 0 or greater.',
            'pay_date.date'          => 'Pay date must be a valid date.',
            'exam_id.exists'         => 'The selected exam is invalid.',
            'due_day.integer'        => 'Due day must be an integer.',
            'due_day.min'            => 'Due day must be between 1 and 31.',
            'due_day.max'            => 'Due day must be between 1 and 31.',
            'food_type.in'           => 'Invalid food type selected.',
            'student_id.exists'      => 'The selected student is invalid.',
            'student_ids.array'      => 'Student IDs must be an array.',
            'student_ids.*.exists'   => 'One or more selected students are invalid.',
        ];
    }

    /**
     * Custom validator checks for duplicate fee name within the same session.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->any()) {
                return;
            }

            $school = \App\Models\School::where('user_id', auth()->id())->first();
            if (!$school) {
                return;
            }

            $id = $this->route('fee_template') ?? $this->route('id');

            $match = [
                'class_id'   => $this->class_id,
                'group_id'   => $this->group_id ?? null,
                'section_id' => $this->section_id ?? null,
                'session_id' => $this->session_id,
            ];

            if ($this->fee_type_name === 'Exams') {
                $query = \App\Models\SchoolFeeTemplate::where('school_id', $school->id)
                    ->where($match)
                    ->where('exam_id', $this->exam_id)
                    ->where('id', '!=', $id);
                if ($query->exists()) {
                    $validator->errors()->add('exam_id', 'An exam fee already exists for this exam in the selected class, group, section and session.');
                    return;
                }
            }

            $query = \App\Models\SchoolFeeTemplate::where('school_id', $school->id)
                ->where($match)
                ->where('fee_type_name', $this->fee_type_name)
                ->where('id', '!=', $id);

            if ($query->exists()) {
                $validator->errors()->add('fee_type_name', 'A fee of this type already exists for the selected class, group, section and session.');
            }
        });
    }

    /**
     * Return a JSON 422 response instead of redirecting (API route).
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'status'  => 'error',
                'message' => 'Validation failed. Please check the form fields.',
                'errors'  => $validator->errors(),
            ], 422)
        );
    }
}
