<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSchoolExpenseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'invoice_no'     => ['nullable', 'string', 'max:255'],
            'expense_date'   => ['required', 'date'],
            'expense_reason' => ['required', 'string', 'max:255'],
            'amount'         => ['required', 'numeric', 'min:0'],
            'balance'        => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'expense_date.required' => 'Expense date is required.',
            'expense_reason.required' => 'Expense reason is required.',
            'amount.required' => 'Amount is required.',
        ];
    }
}