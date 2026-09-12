<?php

namespace App\Http\Requests\Recruitment;

use Illuminate\Foundation\Http\FormRequest;

class StoreWalkInApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage job applications');
    }

    public function rules(): array
    {
        return [
            'job_posting_id' => ['required', 'exists:job_postings,id'],
            'application_source' => ['required', 'in:referral,walk_in,archive'],
            'notes' => ['nullable', 'string'],
            // applicant data (buat ApplicantMasterData baru jika belum ada)
            'applicant.full_name' => ['required', 'string', 'max:255'],
            'applicant.whatsapp_number' => ['required', 'string', 'max:50'],
            'applicant.email' => ['nullable', 'email'],
        ];
    }
}
