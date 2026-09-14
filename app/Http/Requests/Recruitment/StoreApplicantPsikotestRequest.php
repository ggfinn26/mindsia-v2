<?php

namespace App\Http\Requests\Recruitment;

use Illuminate\Foundation\Http\FormRequest;

class StoreApplicantPsikotestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('recruitment.psikotest.create');
    }

    public function rules(): array
    {
        return [
            'psikotest_link' => ['nullable', 'url', 'max:500'],
            'psikotest_date' => ['nullable', 'date'],
        ];
    }
}
