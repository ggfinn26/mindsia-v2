<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class StoreMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name'      => ['required', 'string', 'max:255'],
            'whatsapp_number' => ['required', 'string', 'max:20'],
            'birth_date'     => ['required', 'date'],
            'gender'         => ['required', 'in:M,F'],
            'address'        => ['required', 'string'],
            'city'           => ['required', 'string', 'max:100'],
            'email'          => ['required', 'email', 'max:255', 'unique:member_accounts,email'],
            'password'       => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required'],
        ];
    }
}
