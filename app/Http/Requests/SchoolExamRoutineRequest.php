<?php

namespace App\Http\Requests;

use App\Models\School;
use App\Models\SchoolExamRoutine;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SchoolExamRoutineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $school = School::where('user_id', Auth::id())->first();
        $routineId = $this->route('school_exam_routine') ?? $this->route('id');

        if ($routineId instanceof SchoolExamRoutine) {
            $routineId = $routineId->id;
        }

        return [
            'class_id'   => 'required|exists:school_classes,id',
            'group_id'   => 'nullable|exists:school_groups,id',
            'section_id' => 'nullable|exists:school_sections,id',
            'session_id' => 'required|exists:school_sessions,id',
            'exam_id'    => 'required|exists:school_exam_names,id',
            'subject_id' => [
                'required',
                Rule::unique('school_exam_routines')->where(function ($query) use ($school) {
                    return $query->where('school_id', $school?->id)
                        ->where('class_id', $this->input('class_id'))
                        ->where('group_id', $this->input('group_id'))
                        ->where('section_id', $this->input('section_id'))
                        ->where('session_id', $this->input('session_id'))
                        ->where('exam_id', $this->input('exam_id'));
                })->ignore($routineId),
            ],
            'exam_date'    => 'required|date',
            'start_time'   => 'required',
            'end_time'     => 'required',
            'day_name'     => 'nullable|string',
            'total_hours'  => 'nullable',
        ];
    }

    public function messages(): array
    {
        return [
            'subject_id.unique' => 'A routine already exists for this class, section, group, session, exam and subject.',
        ];
    }
}
