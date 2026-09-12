<?php

namespace App\Http\Requests\Recruitment;

use Illuminate\Foundation\Http\FormRequest;

class UpdateApplicantPsikotestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage recruitment stages');
    }

    public function rules(): array
    {
        return [
            'psikotest_score' => ['nullable', 'integer', 'min:0'],
            'psikotest_notes' => ['nullable', 'string'],
            'psikotest_date' => ['nullable', 'date'],
        ];
    }
}
