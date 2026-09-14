<?php

namespace App\Http\Requests\Kpi;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKpiEvaluationItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        $evaluation = $this->route('kpiEvaluation');

        if (! $evaluation->isDraft()) {
            return false;
        }

        $user = $this->user();

        return $user->can('kpi.evaluation.update')
            && $user->employee?->id === $evaluation->evaluator_employee_id;
    }

    public function rules(): array
    {
        return [
            'actual_value' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
