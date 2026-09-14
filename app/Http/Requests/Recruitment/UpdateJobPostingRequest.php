<?php

namespace App\Http\Requests\Recruitment;

use App\Models\JobPosting;
use Illuminate\Foundation\Http\FormRequest;

class UpdateJobPostingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('recruitment.job_posting.update')
            && $this->route('jobPosting')->status === JobPosting::STATUS_DRAFT;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'job_description' => ['sometimes', 'string'],
            'job_responsibilities' => ['sometimes', 'string'],
            'job_requirements_text' => ['sometimes', 'string'],
            'closing_date' => ['nullable', 'date', 'after:today'],
        ];
    }
}
