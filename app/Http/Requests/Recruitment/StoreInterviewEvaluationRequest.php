<?php

namespace App\Http\Requests\Recruitment;

use Illuminate\Foundation\Http\FormRequest;

class StoreInterviewEvaluationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage recruitment stages');
    }

    public function rules(): array
    {
        return [
            'score_education' => ['required', 'integer', 'between:1,4'],
            'score_experience' => ['required', 'integer', 'between:1,4'],
            'score_personality' => ['required', 'integer', 'between:1,4'],
            'score_communication' => ['required', 'integer', 'between:1,4'],
            'score_problem_solving' => ['required', 'integer', 'between:1,4'],
            // total_score TIDAK diterima dari input — dihitung di service
            'decision' => ['required', 'in:rejected,reserve,accepted'],
            'current_salary' => ['nullable', 'numeric', 'min:0'],
            'desired_salary' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
