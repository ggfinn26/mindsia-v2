<?php

namespace App\Http\Requests\Recruitment;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        // guard: applicant
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [];
        // posting_id dari route param; applicant dari auth
        // validasi UNIQUE (applicant_id, job_posting_id) ditangani DB + service
    }
}
