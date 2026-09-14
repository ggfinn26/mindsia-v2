<?php

namespace App\Http\Requests\Survey;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSurveyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('survey.form.update');
    }

    public function rules(): array
    {
        return [
            'survey_name' => ['sometimes', 'string', 'max:255'],
            'survey_description' => ['sometimes', 'nullable', 'string'],
            'deadline_at' => ['sometimes', 'nullable', 'date'],
        ];
    }
}
