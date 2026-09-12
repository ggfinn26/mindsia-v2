<?php

namespace App\Http\Requests\Recruitment;

use Illuminate\Foundation\Http\FormRequest;

class UpdateApplicantEducationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'institution_name' => ['sometimes', 'string', 'max:255'],
            'education_level' => ['sometimes', 'in:sd,smp,sma,d1,d2,d3,d4,s1,s2,s3'],
            'major' => ['sometimes', 'string', 'max:150'],
            'gpa' => ['sometimes', 'numeric', 'between:0,4'],
            'start_date' => ['sometimes', 'date'],
            'graduation_date' => ['nullable', 'date'],
        ];
    }
}
