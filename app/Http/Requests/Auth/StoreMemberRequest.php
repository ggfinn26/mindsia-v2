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
            'address' => ['required', 'string'],
            'institution_id' => ['required', 'exists:institutions,id'],
            'program_id' => ['nullable', 'exists:programs,id'],
            'referred_by_code' => ['nullable', 'string', 'max:20'],
            'password' => ['required', Password::defaults(), 'confirmed'],
            'password_confirmation' => ['required'],
        ];
    }
}
