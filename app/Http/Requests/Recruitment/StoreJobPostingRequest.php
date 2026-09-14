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
            'title' => ['required', 'string', 'max:255'],
            'job_description' => ['required', 'string'],
            'job_responsibilities' => ['required', 'string'],
            'job_requirements_text' => ['required', 'string'],
            'closing_date' => ['nullable', 'date', 'after:today'],
        ];
    }
}
