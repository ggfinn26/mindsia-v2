<?php

namespace App\Http\Requests\Recruitment;

use Illuminate\Foundation\Http\FormRequest;

class UpdateApplicantCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        $course = $this->route('course');

        return $this->user() !== null
            && $course->applicant_id === $this->user()->applicantData?->id;
    }

    public function rules(): array
    {
        return [
            'course_name' => ['sometimes', 'string', 'max:200'],
            'issuer_name' => ['sometimes', 'string', 'max:200'],
            'issue_date' => ['nullable', 'date'],
            'expiry_date' => ['nullable', 'date'],
            'certificate_path' => ['nullable', 'string', 'max:500'],
        ];
    }
}
