<?php

namespace App\Http\Requests\Toefl;

use Illuminate\Foundation\Http\FormRequest;

class StartToeflSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('member')->check();
    }

    public function rules(): array
    {
        return [
            'password' => ['nullable', 'string'],
        ];
    }
}
