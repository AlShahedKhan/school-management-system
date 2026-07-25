<?php

namespace App\Http\Requests;

use App\Models\School;
use App\Models\SchoolSection;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SchoolSectionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    protected function getSchoolId()
    {
        $school = School::where('user_id', Auth::id())->first();
        if (!$school) {
            abort(404, 'School profile not found.');
        }
        return $school->id;
    }

    public function rules()
    {
        $sectionId = $this->route('section') ?? $this->route('school_section') ?? $this->route('id');
        if ($sectionId instanceof SchoolSection) {
            $sectionId = $sectionId->id;
        }

        return [
            'class_id' => 'required|exists:school_classes,id',
            'group_id' => 'nullable|exists:school_groups,id',
            'section_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('school_sections')->where(function ($query) {
                    return $query->where('school_id', $this->getSchoolId())
                        ->where('class_id', $this->input('class_id'))
                        ->where('group_id', $this->input('group_id'));
                })->ignore($sectionId),
            ],
        ];
    }

    public function messages()
    {
        return [
            'class_id.required' => 'Please select a class.',
            'section_name.required' => 'Please enter a section name.',
            'section_name.unique' => 'This section already exists for the selected class and group.',
        ];
    }
}
