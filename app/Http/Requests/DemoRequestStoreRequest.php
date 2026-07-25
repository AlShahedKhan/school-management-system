<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DemoRequestStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge($this->trimPayload($this->all()));
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'school_name' => ['required', 'string', 'max:160'],
            'phone' => ['required', 'string', 'max:30'],
            'student_qty' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'booking_date' => ['nullable', 'date'],
            'booking_time' => ['nullable', 'date_format:H:i'],
            'email' => ['nullable', 'email', 'max:160'],
            'message' => ['nullable', 'string', 'max:1000'],
        ];
    }

    private function trimPayload(mixed $value): mixed
    {
        if (is_array($value)) {
            return array_map(fn ($item) => $this->trimPayload($item), $value);
        }

        return is_string($value) ? trim($value) : $value;
    }
}
