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
            'birth_place' => ['required', 'string', 'max:255'],
            'birthdate' => ['required', 'date'],
            'whatsapp_number' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255', 'unique:members_data,email'],
            'instagram' => ['nullable', 'string', 'max:255'],
            'father_name' => ['required', 'string', 'max:255'],
            'mother_name' => ['required', 'string', 'max:255'],
            'father_occupation' => ['required', 'string', 'max:255'],
            'mother_occupation' => ['required', 'string', 'max:255'],
            'father_whatsapp' => ['required', 'string', 'max:20'],
            'mother_whatsapp' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string'],
            'branch_id' => ['required', 'exists:branches,id'],
            'institution_id' => ['nullable', 'exists:institutions,id', 'required_without:institution_name_custom'],
            'institution_name_custom' => ['nullable', 'string', 'max:255'],
            'education_level' => ['nullable', 'string', 'in:SD,SMP,SMA,SMK,D1,D2,D3,D4,S1,S2,S3,Umum'],
            'class_or_faculty' => ['nullable', 'string', 'max:100'],
            'program_id' => ['nullable', 'integer', 'exists:programs,id'],
            'referred_by_employee_id' => ['nullable', 'integer', 'exists:employees,id'],
            'create_account' => ['nullable', 'boolean'],
        ];
    }
}
