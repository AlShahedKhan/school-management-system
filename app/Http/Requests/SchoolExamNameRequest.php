<?php

namespace App\Http\Requests;

use App\Models\School;
use App\Models\SchoolExamName;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SchoolExamNameRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $examId = $this->route('school_exam_name') ?? $this->route('id');
        if ($examId instanceof SchoolExamName) {
            $examId = $examId->id;
        }

        return [
            'class_id'   => 'required|integer|exists:school_classes,id',
            'section_id' => 'required|integer|exists:school_sections,id',
            'group_id'   => 'nullable|integer|exists:school_groups,id',
            'session_id' => 'required|integer|exists:school_sessions,id',
            'exam_start_date' => ['nullable', 'date'],
            'exam_end_date' => ['nullable', 'date', 'after_or_equal:exam_start_date'],
            'exam_name'  => [
                'required',
                'string',
                'max:255',
                Rule::unique('school_exam_names')->where(function ($query) {
                    return $query->where('school_id', $this->getSchoolId())
                        ->where('class_id', $this->input('class_id'))
                        ->where('section_id', $this->input('section_id'))
                        ->where('group_id', $this->input('group_id'))
                        ->where('session_id', $this->input('session_id'));
                })->ignore($examId),
            ],
        ];
    }

    protected function getSchoolId(): int
    {
        $schoolId = School::where('user_id', Auth::id())->value('id');

        if (! $schoolId) {
            abort(403, 'School profile not found.');
        }

        return (int) $schoolId;
    }

    public function messages()
    {
        return [
            'exam_name.unique' => 'An exam with this name already exists for this class, section, group and session.',
            'exam_end_date.after_or_equal' => 'Exam end date must be the same as or after the exam start date.',
        ];
    }
}
