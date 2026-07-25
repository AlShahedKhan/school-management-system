<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SchoolRoutineRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $routineId = $this->route('school_routine') ?? $this->route('id');

        return [
            'class_id' => 'required|exists:school_classes,id',
            'subject_id' => 'required|exists:school_subjects,id',
            'teacher_id' => 'required|exists:teachers,id',
            'day_name' => 'required|string|max:255',
            'start_time' => 'required',
            'end_time' => 'required|after_or_equal:start_time',
            'group_id' => 'nullable|exists:school_groups,id',
            'section_id' => 'nullable|exists:school_sections,id',
        ];
    }

    public function messages()
    {
        return [
            'class_id.required' => 'Please select a class.',
            'class_id.exists' => 'The selected class is invalid.',
            'subject_id.required' => 'Please select a subject.',
            'subject_id.exists' => 'The selected subject is invalid.',
            'teacher_id.required' => 'Please select a teacher.',
            'teacher_id.exists' => 'The selected teacher is invalid.',
            'day_name.required' => 'Please enter a day name.',
            'start_time.required' => 'Please enter a start time.',
            'end_time.required' => 'Please enter an end time.',
            'end_time.after_or_equal' => 'End time must be same or later than start time.',
            'group_id.exists' => 'The selected group is invalid.',
            'section_id.exists' => 'The selected section is invalid.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 422));
    }
}
