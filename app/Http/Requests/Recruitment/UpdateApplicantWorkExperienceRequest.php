<?php

namespace App\Http\Requests\Recruitment;

use Illuminate\Foundation\Http\FormRequest;

class UpdateApplicantWorkExperienceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'company_name' => ['sometimes', 'string', 'max:200'],
            'position' => ['sometimes', 'string', 'max:200'],
            'start_date' => ['sometimes', 'date'],
            'end_date' => ['nullable', 'date'],
            'reason_for_leaving' => ['nullable', 'string'],
        ];
    }
}
