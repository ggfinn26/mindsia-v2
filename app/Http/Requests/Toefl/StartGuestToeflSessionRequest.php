<?php

namespace App\Http\Requests\Toefl;

use Illuminate\Foundation\Http\FormRequest;

class StartGuestToeflSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'guest_name' => ['required', 'string', 'max:255'],
            'guest_whatsapp' => ['required', 'string', 'max:50'],
            'guest_city' => ['required', 'string', 'max:100'],
            'guest_email' => ['nullable', 'email', 'max:255'],
            'guest_instagram' => ['nullable', 'string', 'max:100'],
            'guest_institution' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'string'],
        ];
    }
}
