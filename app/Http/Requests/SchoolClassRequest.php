<?php

namespace App\Http\Requests;

use App\Models\School;
use App\Models\SchoolClass;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SchoolClassRequest extends FormRequest
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
        $classId = $this->route('class') ?? $this->route('school_class') ?? $this->route('id');
        if ($classId instanceof SchoolClass) {
            $classId = $classId->id;
        }

        return [
            'class_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('school_classes')->where(function ($query) {
                    return $query->where('school_id', $this->getSchoolId());
                })->ignore($classId),
            ],
        ];
    }

    public function messages()
    {
        return [
            'class_name.required' => 'Please enter a class name.',
            'class_name.unique' => 'A class with this name already exists.',
        ];
    }
}
