<?php

namespace App\Http\Requests\Recruitment;

use Illuminate\Foundation\Http\FormRequest;

class StoreApplicantEducationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'institution_name' => ['required', 'string', 'max:255'],
            'education_level' => ['required', 'in:sd,smp,sma,d1,d2,d3,d4,s1,s2,s3'],
            'major' => ['required', 'string', 'max:150'],
            'gpa' => ['required', 'numeric', 'between:0,4'],
            'start_date' => ['required', 'date'],
            'graduation_date' => ['nullable', 'date', 'after:start_date'],
        ];
    }
}
