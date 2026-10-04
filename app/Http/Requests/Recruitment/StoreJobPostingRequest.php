<?php

namespace App\Http\Requests\Recruitment;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobPostingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('recruitment.job_posting.create');
    }

    public function rules(): array
    {
        return [
            'job_permintaan_id' => ['required', 'exists:job_permintaan,id'],
            'position_id' => ['required', 'exists:positions,id'],
            'title' => ['required', 'string', 'max:255'],
            'job_description' => ['required', 'string'],
            'job_responsibilities' => ['required', 'string'],
            'job_requirements_text' => ['required', 'string'],
            'closing_date' => ['nullable', 'date', 'after:today'],
            'requirements_auto.minimum_age' => ['nullable', 'integer', 'min:15', 'max:99'],
            'requirements_auto.maximum_age' => ['nullable', 'integer', 'min:15', 'max:99', 'gte:requirements_auto.minimum_age'],
            'requirements_auto.minimum_year_experience' => ['nullable', 'integer', 'min:0'],
            'requirements_auto.minimum_education_level' => ['nullable', 'string', 'max:50'],
            'requirements_manual' => ['nullable', 'array'],
            'requirements_manual.*.criteria_name' => ['required_with:requirements_manual', 'string', 'max:100'],
            'requirements_manual.*.criteria_desc' => ['required_with:requirements_manual', 'string'],
        ];
    }
}
