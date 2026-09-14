<?php

namespace App\Http\Requests\Recruitment;

use App\Models\JobApplication;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreJobApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($v) {
            $applicantDataId = $this->user()->applicantData?->id;
            $jobPostingId = $this->route('jobPosting')?->id;

            if ($applicantDataId && $jobPostingId) {
                $exists = JobApplication::where('applicant_id', $applicantDataId)
                    ->where('job_posting_id', $jobPostingId)
                    ->exists();

                if ($exists) {
                    $v->errors()->add('job_posting_id', 'Anda sudah mendaftar untuk lowongan ini.');
                }
            }
        });
    }
}
