<?php

namespace App\Http\Requests\Letter;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInLetterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('letter.in.update');
    }

    public function rules(): array
    {
        return [
            'sender_name' => ['sometimes', 'string', 'max:255'],
            'letter_date' => ['nullable', 'date'],
            'receive_date' => ['sometimes', 'date'],
            'letter_number' => ['nullable', 'string', 'max:100'],
            'subject' => ['sometimes', 'string', 'max:255'],
            'pic_employee_id' => ['nullable', 'exists:employees,id'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
