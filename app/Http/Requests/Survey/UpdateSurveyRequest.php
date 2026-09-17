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
            'survey_name' => ['required', 'string', 'max:255'],
            'survey_description' => ['nullable', 'string'],
            'deadline_at' => ['nullable', 'date'],
            'questions' => ['required', 'array', 'min:1'],
            'questions.*.question_text' => ['required', 'string'],
            'questions.*.question_type' => ['required', 'in:single_choice,multiple_choice,scale,text'],
            'questions.*.scale_min' => ['nullable', 'integer'],
            'questions.*.scale_max' => ['nullable', 'integer', 'gt:questions.*.scale_min'],
            'questions.*.scale_min_label' => ['nullable', 'string', 'max:100'],
            'questions.*.scale_max_label' => ['nullable', 'string', 'max:100'],
            'questions.*.choices' => ['nullable', 'array'],
            'questions.*.choices.*.choice_text' => ['required_with:questions.*.choices', 'string'],
        ];
    }
}
