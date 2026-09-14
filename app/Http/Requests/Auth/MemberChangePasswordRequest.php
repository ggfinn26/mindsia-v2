<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class MemberChangePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('member') !== null;
    }

    public function rules(): array
    {
        return [
            'current_password' => ['required', 'current_password:member'],
            'password' => ['required', Password::defaults(), 'different:current_password'],
        ];
    }
}
