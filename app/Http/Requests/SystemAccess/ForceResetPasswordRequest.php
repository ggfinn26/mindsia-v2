<?php

namespace App\Http\Requests\SystemAccess;

use Illuminate\Foundation\Http\FormRequest;

class ForceResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('auth.user.force_reset_password');
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
            'current_password' => ['required', 'string'],
        ];
    }
}
