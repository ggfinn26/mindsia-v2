<?php

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;

class StoreMemberDataRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('member.manage');
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:L,P'],
            'birthdate' => ['required', 'date'],
            'whatsapp_number' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255', 'unique:members_data,email'],
            'instagram' => ['nullable', 'string', 'max:255'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'mother_name' => ['nullable', 'string', 'max:255'],
            'father_occupation' => ['nullable', 'string', 'max:255'],
            'mother_occupation' => ['nullable', 'string', 'max:255'],
            'father_whatsapp' => ['nullable', 'string', 'max:20'],
            'mother_whatsapp' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'institution_id' => ['required', 'integer', 'exists:institutions,id'],
            'program_id' => ['nullable', 'integer', 'exists:programs,id'],
            'referred_by_employee_id' => ['nullable', 'integer', 'exists:employees,id'],
            'create_account' => ['nullable', 'boolean'],
        ];
    }
}
