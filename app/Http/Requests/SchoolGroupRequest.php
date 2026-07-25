<?php

namespace App\Http\Requests;

use App\Models\School;
use App\Models\SchoolGroup;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SchoolGroupRequest extends FormRequest
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
        $groupId = $this->route('group') ?? $this->route('school_group') ?? $this->route('id');
        if ($groupId instanceof SchoolGroup) {
            $groupId = $groupId->id;
        }

        return [
            'class_id' => 'required|exists:school_classes,id',
            'group_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('school_groups')->where(function ($query) {
                    return $query->where('school_id', $this->getSchoolId())
                        ->where('class_id', $this->input('class_id'));
                })->ignore($groupId),
            ],
        ];
    }

    public function messages()
    {
        return [
            'class_id.required' => 'Please select a class.',
            'group_name.required' => 'Please enter a group name.',
            'group_name.unique' => 'This group already exists for the selected class.',
        ];
    }
}
