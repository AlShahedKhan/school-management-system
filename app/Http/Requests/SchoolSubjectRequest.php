<?php

namespace App\Http\Requests;

use App\Models\School;
use App\Models\SchoolExamGrade;
use App\Models\SchoolSubject;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SchoolSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $subjectId = $this->route('school_subject') ?? $this->route('school_subjects') ?? $this->route('id');
        if ($subjectId instanceof SchoolSubject) {
            $subjectId = $subjectId->id;
        }

        return [
            'class_id'      => ['required', 'integer', 'exists:school_classes,id'],
            'group_id'      => ['required', 'integer', 'exists:school_groups,id'],
            'section_id'    => ['required', 'integer', 'exists:school_sections,id'],
            'subject_name'  => [
                'required', 'string',
                Rule::unique('school_subjects')->where(function ($query) {
                    return $query->where('school_id', $this->getSchoolId())
                        ->where('class_id', $this->input('class_id'))
                        ->where('group_id', $this->input('group_id'))
                        ->where('section_id', $this->input('section_id'));
                })->ignore($subjectId),
            ],
            'grade_id'      => ['nullable', 'integer', 'exists:school_exam_grades,id'],
            'subject_code'  => [
                'nullable', 'string',
                Rule::unique('school_subjects')->where(function ($query) {
                    return $query->where('school_id', $this->getSchoolId())
                        ->where('class_id', $this->input('class_id'))
                        ->where('group_id', $this->input('group_id'))
                        ->where('section_id', $this->input('section_id'));
                })->ignore($subjectId),
            ],
            'marks'                     => ['required', 'array'],
            'marks.tutorial_mark'       => ['required', 'integer', 'min:0'],
            'marks.mcq_mark'            => ['required', 'integer', 'min:0'],
            'marks.writing_mark'        => ['required', 'integer', 'min:0'],
            'marks.practical_mark'      => ['required', 'integer', 'min:0'],
            'marks.total_mark'          => ['required', 'integer', 'min:1'],
            'fail_mark'                 => ['required', 'integer', 'min:0'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $gradeId = $this->input('grade_id');
            $marks = $this->input('marks', []);
            $totalMark = $marks['total_mark'] ?? null;
            $failMark = $this->input('fail_mark');

            if (!$gradeId || ($totalMark === null && $failMark === null)) {
                return;
            }

            $grade = SchoolExamGrade::find($gradeId);
            if (!$grade) {
                return;
            }

            $maxMark = (int) $grade->full_mark;

            foreach (['tutorial_mark', 'mcq_mark', 'writing_mark', 'practical_mark'] as $field) {
                $value = $marks[$field] ?? null;
                if ($value !== null && (int) $value > $maxMark) {
                    $validator->errors()->add($field, ucfirst(str_replace('_', ' ', $field)) . " cannot exceed {$maxMark}.");
                }
            }
            if ($totalMark !== null && (int) $totalMark > $maxMark) {
                $validator->errors()->add('total_mark', "Total Mark cannot exceed {$maxMark}.");
            }
            if ($failMark !== null && (int) $failMark > $maxMark) {
                $validator->errors()->add('fail_mark', "Fail Mark cannot exceed {$maxMark}.");
            }
            if ($totalMark !== null && $failMark !== null && (int) $failMark > (int) $totalMark) {
                $validator->errors()->add('fail_mark', 'Fail Mark cannot be greater than Total Mark.');
            }
        });
    }

    protected function getSchoolId()
    {
        $school = School::where('user_id', Auth::id())->first();
        return $school ? $school->id : null;
    }

    public function messages(): array
    {
        return [
            'class_id.required'                 => 'Class is required.',
            'class_id.exists'                   => 'Selected class is invalid.',
            'section_id.required'               => 'Section is required.',
            'group_id.exists'                   => 'Selected group is invalid.',
            'group_id.required'                 => 'Group is required.',
            'section_id.exists'                 => 'Selected section is invalid.',
            'subject_name.required'             => 'Subject name is required.',
            'subject_name.unique'               => 'This subject name already exists for this class, group and section.',
            'subject_code.unique'               => 'This subject code already exists for this class, group and section.',
            'marks.required'                    => 'Mark values are required.',
            'marks.tutorial_mark.required'      => 'Tutorial Mark is required.',
            'marks.tutorial_mark.integer'       => 'Tutorial Mark must be an integer.',
            'marks.tutorial_mark.min'           => 'Tutorial Mark minimum value is 0.',
            'marks.mcq_mark.required'           => 'MCQ Mark is required.',
            'marks.mcq_mark.integer'            => 'MCQ Mark must be an integer.',
            'marks.mcq_mark.min'                => 'MCQ Mark minimum value is 0.',
            'marks.writing_mark.required'       => 'Writing Mark is required.',
            'marks.writing_mark.integer'        => 'Writing Mark must be an integer.',
            'marks.writing_mark.min'            => 'Writing Mark minimum value is 0.',
            'marks.practical_mark.required'     => 'Practical Mark is required.',
            'marks.practical_mark.integer'      => 'Practical Mark must be an integer.',
            'marks.practical_mark.min'          => 'Practical Mark minimum value is 0.',
            'marks.total_mark.required'         => 'Total Mark is required.',
            'marks.total_mark.integer'          => 'Total Mark must be an integer.',
            'marks.total_mark.min'              => 'Total Mark must be greater than 0.',
            'fail_mark.required'                => 'Fail Mark is required.',
            'fail_mark.integer'                 => 'Fail Mark must be an integer.',
            'fail_mark.min'                     => 'Fail Mark minimum value is 0.',
        ];
    }

    protected function prepareForValidation()
    {
        $marks = [];
        foreach (['tutorial_mark', 'mcq_mark', 'writing_mark', 'practical_mark', 'total_mark'] as $field) {
            $value = $this->input($field);
            if ($value !== null && $value !== '') {
                $marks[$field] = (int) $value;
            }
        }

        if (!empty($marks)) {
            $this->merge(['marks' => $marks]);
        }
    }
}
