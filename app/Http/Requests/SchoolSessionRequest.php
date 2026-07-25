<?php

namespace App\Http\Requests;

use App\Models\School;
use App\Models\SchoolSession;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SchoolSessionRequest extends FormRequest
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
        $sessionId = $this->route('school_session') ?? $this->route('id');
        if ($sessionId instanceof SchoolSession) {
            $sessionId = $sessionId->id;
        }

        return [
            'class_id'     => 'required|exists:school_classes,id',
            'group_id'     => 'nullable|exists:school_groups,id',
            'section_id'   => 'nullable|exists:school_sections,id',
            'session_year' => [
                'required',
                'string',
                'max:255',
                Rule::unique('school_sessions')->where(function ($query) {
                    return $query->where('school_id', $this->getSchoolId())
                        ->where('class_id', $this->input('class_id'))
                        ->where('group_id', $this->input('group_id'))
                        ->where('section_id', $this->input('section_id'));
                })->ignore($sessionId),
            ],
            'start_date'   => 'required|date',
            'end_date'     => 'required|date|after_or_equal:start_date',
            'total_days'     => 'required|integer',
            'remaining_days' => 'nullable|integer',
        ];
    }

    public function messages()
    {
        return [
            'class_id.required' => 'Please select a class.',
            'session_year.required' => 'Please enter a session year.',
            'session_year.unique' => 'A session for this Class / Group / Section with the same year already exists.',
        ];
    }
}
