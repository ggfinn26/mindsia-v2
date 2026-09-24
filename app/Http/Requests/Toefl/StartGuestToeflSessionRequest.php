<?php

namespace App\Http\Requests\Toefl;

use Illuminate\Foundation\Http\FormRequest;

class StartGuestToeflSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->guest_whatsapp) {
            $number = preg_replace('/[^0-9]/', '', $this->guest_whatsapp);
            $number = ltrim($number, '0');
            $this->merge(['guest_whatsapp' => '+62'.$number]);
        }
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
