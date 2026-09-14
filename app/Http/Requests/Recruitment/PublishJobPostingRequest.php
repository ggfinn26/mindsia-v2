<?php

namespace App\Http\Requests\Recruitment;

use App\Models\JobPosting;
use Illuminate\Foundation\Http\FormRequest;

class PublishJobPostingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('recruitment.job_posting.publish')
            && $this->route('jobPosting')->status === JobPosting::STATUS_DRAFT;
    }

    public function rules(): array
    {
        return [
            'closing_date' => ['nullable', 'date', 'after:today'],
        ];
    }
}
