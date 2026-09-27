<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreApplicantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'whatsapp_number' => ['required', 'string', 'min:8', 'max:15'],
            'birth_date' => ['nullable', 'date', 'before_or_equal:' . now()->subYears(17)->format('Y-m-d')],
            'gender' => ['nullable', 'in:male,female'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'email' => ['required', 'email', 'unique:applicant_accounts,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed', Password::defaults()],
            'password_confirmation' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'Email ini sudah terdaftar.',
            'whatsapp_number.required' => 'Nomor WhatsApp wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'birth_date.before_or_equal' => 'Pelamar harus berusia minimal 17 tahun.',
        ];
    }
}
