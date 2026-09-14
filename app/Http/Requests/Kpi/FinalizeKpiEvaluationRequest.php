<?php

namespace App\Http\Requests\Kpi;

use Illuminate\Foundation\Http\FormRequest;

class FinalizeKpiEvaluationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $evaluation = $this->route('kpiEvaluation');

        if (! $evaluation->isDraft()) {
            return false;
        }

        $user = $this->user();

        return $user->can('kpi.evaluation.finalize')
            && $user->employee?->id === $evaluation->evaluator_employee_id;
    }

    public function rules(): array
    {
        return [
            'evaluator_notes' => ['nullable', 'string'],
        ];
    }
}
