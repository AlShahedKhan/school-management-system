<?php

namespace App\Http\Requests;

use App\Models\School;
use App\Models\SchoolClass;
use App\Models\SchoolExamSchedule;
use App\Models\SchoolGroup;
use App\Models\SchoolSection;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SchoolExamScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && in_array($this->user()?->role, ['admin', 'school'], true);
    }

    public function rules(): array
    {
        $scheduleId = $this->route('school_exam_schedule') ?? $this->route('id');
        $school = School::where('user_id', Auth::id())->first();

        $groupName = $this->input('group_name') ?: null;
        $sectionName = $this->input('section_name') ?: null;

        $rules = [
            'class_name' => ['required', 'string', 'max:255'],
            'group_name' => ['nullable', 'string', 'max:255'],
            'section_name' => ['nullable', 'string', 'max:255'],
            'session_name' => ['required', 'string', 'max:255'],
            'exam_name' => ['required', 'string', 'max:255'],
            'publish_date' => ['required', 'date'],
            'publish_time' => ['required', 'regex:/^\d{2}:\d{2}(?::\d{2})?$/'],
            'status' => ['nullable', 'in:Active,Inactive'],
            'total_subject' => ['nullable', 'integer'],
            'submitted_subject' => ['nullable', 'integer'],
            'remaining_subject' => ['nullable', 'integer'],
        ];

        if ($school) {
            $rules['class_name'][] = function ($attribute, $value, $fail) use ($school) {
                $class = SchoolClass::where('school_id', $school->id)->where('class_name', $value)->first();
                if (!$class) {
                    $fail('Selected class is invalid for this school.');
                }
            };

            if ($groupName) {
                $rules['group_name'][] = function ($attribute, $value, $fail) use ($school) {
                    $group = SchoolGroup::where('school_id', $school->id)->where('group_name', $value)->first();
                    if (!$group) {
                        $fail('Selected group is invalid for this school.');
                    }
                };
            }

            if ($sectionName) {
                $rules['section_name'][] = function ($attribute, $value, $fail) use ($school) {
                    $section = SchoolSection::where('school_id', $school->id)->where('section_name', $value)->first();
                    if (!$section) {
                        $fail('Selected section is invalid for this school.');
                    }
                };
            }
        }

        $rules['class_name'][] = function ($attribute, $value, $fail) use ($scheduleId, $school, $groupName, $sectionName) {
            if (!$school) {
                return;
            }

            $query = SchoolExamSchedule::where('school_id', $school->id)
                ->where('class_name', $value)
                ->where('session_name', $this->input('session_name'))
                ->where('exam_name', $this->input('exam_name'));

            if ($scheduleId) {
                $query->where('id', '!=', $scheduleId);
            }

            if ($groupName) {
                $query->where('group_name', $groupName);
            } else {
                $query->whereNull('group_name');
            }

            if ($sectionName) {
                $query->where('section_name', $sectionName);
            } else {
                $query->whereNull('section_name');
            }

            if ($query->exists()) {
                $fail('A result live schedule already exists for this class, group, section, session and exam.');
            }
        };

        return $rules;
    }

    public function messages(): array
    {
        return [
            'class_name.required' => 'Class is required.',
            'session_name.required' => 'Session is required.',
            'exam_name.required' => 'Exam is required.',
            'publish_date.required' => 'Publish date is required.',
            'publish_time.required' => 'Publish time is required.',
            'publish_date.date' => 'Publish date must be a valid date.',
            'publish_time.regex' => 'Publish time must be in HH:MM or HH:MM:SS format.',
            'status.in' => 'Status must be Active or Inactive.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $school = School::where('user_id', Auth::id())->first();
            if (!$school) {
                return;
            }

            $className = $this->input('class_name');
            $groupName = $this->input('group_name') ?: null;
            $sectionName = $this->input('section_name') ?: null;
            $sessionName = $this->input('session_name');
            $examName = $this->input('exam_name');

            $class = SchoolClass::where('school_id', $school->id)->where('class_name', $className)->first();
            $group = $groupName ? SchoolGroup::where('school_id', $school->id)->where('group_name', $groupName)->first() : null;
            $section = $sectionName ? SchoolSection::where('school_id', $school->id)->where('section_name', $sectionName)->first() : null;

            $subjectQuery = DB::table('school_subjects')->where('school_id', $school->id);
            if ($class) {
                $subjectQuery->where('class_id', $class->id);
            }
            if ($group) {
                $subjectQuery->where('group_id', $group->id);
            }
            if ($section) {
                $subjectQuery->where('section_id', $section->id);
            }

            $totalSubjects = (clone $subjectQuery)->count();
            if ($totalSubjects === 0) {
                return;
            }

            $submittedQuery = DB::table('school_exam_marks')
                ->where('school_id', $school->id)
                ->where('class_name', $className)
                ->where('session_name', $sessionName)
                ->where('exam_name', $examName);

            if ($groupName) {
                $submittedQuery->where('group_name', $groupName);
            }
            if ($sectionName) {
                $submittedQuery->where('section_name', $sectionName);
            }

            $submittedSubjects = (clone $submittedQuery)->distinct('subject_name')->count('subject_name');

            if ($submittedSubjects < $totalSubjects) {
                $validator->errors()->add('base', 'Selected Class, Group, Section, Session and Exam do not have all subject marks submitted. Please submit marks for every subject before creating a Result Live Schedule.');
            }
        });
    }
}
