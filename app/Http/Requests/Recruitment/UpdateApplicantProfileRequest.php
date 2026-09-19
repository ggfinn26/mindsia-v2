<?php

namespace App\Http\Requests\Recruitment;

use Illuminate\Foundation\Http\FormRequest;

class UpdateApplicantProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        // guard: applicant — diri sendiri
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['sometimes', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'whatsapp_number' => ['sometimes', 'string', 'max:50'],
            'birth_date' => ['nullable', 'date'],
            'gender' => ['nullable', 'in:male,female'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'cv_path' => ['nullable', 'string', 'max:500'],
        ];
    }
}
