<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class AdminResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->can('auth.user.force_reset_password') ?? false;
    }

    public function rules(): array
    {
        return [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults()],
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.current_password' => 'Password yang Anda masukkan salah.',
            'password.required' => 'Password baru harus diisi.',
        ];
    }
}
