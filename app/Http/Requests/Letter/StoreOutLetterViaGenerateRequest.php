<?php

namespace App\Http\Requests\Letter;

use Illuminate\Foundation\Http\FormRequest;

class StoreOutLetterViaGenerateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('letter.generate.create');
    }

    public function rules(): array
    {
        return [
            'letter_template_id' => ['required', 'exists:letter_templates,id'],
            'branch_id' => ['required', 'exists:branches,id'],
            'signer_employee_id' => ['nullable', 'exists:employees,id'],
            'employee_id' => ['nullable', 'exists:employees,id'],
            'institution' => ['nullable', 'string', 'max:255'],
            'recipient' => ['nullable', 'string', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'manual_vars' => ['nullable', 'array'],
            'manual_vars.*' => ['nullable', 'string'],
        ];
    }
}
