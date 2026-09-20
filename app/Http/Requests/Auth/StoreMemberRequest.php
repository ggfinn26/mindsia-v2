<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:L,P'],
            'birthdate' => ['required', 'date', 'before:'.now()->subYears(6)->format('Y-m-d')],
            'birth_place' => ['required', 'string', 'max:255'],
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
            'program_id' => ['nullable', 'exists:programs,id'],
            'referred_by_code' => ['nullable', 'string', 'max:20'],
            'education_level' => ['nullable', 'string', 'in:SD,SMP,SMA,SMK,D1,D2,D3,D4,S1,S2,S3,Umum'],
            'class_or_faculty' => ['nullable', 'string', 'max:100'],
            'password' => ['required', Password::defaults(), 'confirmed'],
            'password_confirmation' => ['required'],
        ];
    }
}
