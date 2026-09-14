<?php

namespace App\Http\Requests\Letter;

use Illuminate\Foundation\Http\FormRequest;

class StoreInLetterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('letter.in.create');
    }

    public function rules(): array
    {
        return [
            'branch_id' => ['required', 'exists:branches,id'],
            'sender_name' => ['required', 'string', 'max:255'],
            'letter_date' => ['nullable', 'date'],
            'receive_date' => ['required', 'date'],
            'letter_number' => ['nullable', 'string', 'max:100'],
            'subject' => ['required', 'string', 'max:255'],
            'file' => ['required', 'file', 'max:20480', 'mimes:pdf,jpg,jpeg,png'],
            'pic_employee_id' => ['nullable', 'exists:employees,id'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
