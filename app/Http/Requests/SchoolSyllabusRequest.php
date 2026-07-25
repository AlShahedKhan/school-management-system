<?php

namespace App\Http\Requests;

use App\Models\School;
use App\Models\SchoolSyllabus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SchoolSyllabusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $schoolId = $this->getSchoolId();
        $syllabusId = $this->route('school_syllabus') ?? $this->route('id');

        if ($syllabusId instanceof SchoolSyllabus) {
            $syllabusId = $syllabusId->id;
        }

        $groupId = $this->input('group_id');
        $sectionId = $this->input('section_id');

        return [
            'session_id' => ['required', 'exists:school_sessions,id'],
            'class_id'   => ['required', 'exists:school_classes,id'],
            'group_id'   => ['required', 'exists:school_groups,id'],
            'section_id' => ['required', 'exists:school_sections,id'],
            'subject_id' => ['required', 'exists:school_subjects,id'],
            'exam_id'    => [
                'required',
                'exists:school_exam_names,id',
                Rule::unique('school_syllabuses')->where(function ($query) use ($schoolId, $groupId, $sectionId) {
                    return $query->where('school_id', $schoolId)
                        ->where('session_id', $this->input('session_id'))
                        ->where('class_id', $this->input('class_id'))
                        ->where('group_id', $groupId)
                        ->where('section_id', $sectionId)
                        ->where('subject_id', $this->input('subject_id'));
                })->ignore($syllabusId),
            ],
            'start_page' => ['required', 'string', 'max:255'],
            'end_page'   => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'group_id.required' => 'The group field is required.',
            'section_id.required' => 'The section field is required.',
            'start_page.required' => 'The start page field is required.',
            'end_page.required' => 'The end page field is required.',
            'exam_id.required' => 'The exam field is required.',
            'exam_id.unique' => 'A syllabus already exists for this session, class, group, section, and subject with this exam.',
        ];
    }

    protected function getSchoolId(): ?int
    {
        $school = School::where('user_id', Auth::id())->first();

        return $school?->id;
    }
}
