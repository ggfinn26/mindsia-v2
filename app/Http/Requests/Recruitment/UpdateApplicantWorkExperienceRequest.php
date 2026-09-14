<?php

namespace App\Http\Requests\Recruitment;

use Illuminate\Foundation\Http\FormRequest;

class UpdateApplicantWorkExperienceRequest extends FormRequest
{
    public function authorize(): bool
    {
        $experience = $this->route('experience');

        return $this->user() !== null
            && $experience->applicant_id === $this->user()->applicantData?->id;
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
