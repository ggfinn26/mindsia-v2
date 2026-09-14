<?php

namespace App\Http\Requests\Survey;

use Illuminate\Foundation\Http\FormRequest;

class AssignSurveyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('survey.assignment.create');
    }

    public function rules(): array
    {
        return [
            'member_ids' => ['nullable', 'array'],
            'member_ids.*' => ['integer', 'exists:members_data,id'],
            'employee_ids' => ['nullable', 'array'],
            'employee_ids.*' => ['integer', 'exists:employees,id'],
        ];
    }
}
