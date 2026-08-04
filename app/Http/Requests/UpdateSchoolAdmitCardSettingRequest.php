<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSchoolAdmitCardSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'school';
    }

    public function rules(): array
    {
        return [
            'instructions_en' => ['required', 'array', 'size:3'],
            'instructions_en.*' => ['required', 'string', 'max:300'],
            'instructions_bn' => ['required', 'array', 'size:3'],
            'instructions_bn.*' => ['required', 'string', 'max:300'],
        ];
    }
}
