<?php

namespace App\Http\Requests\Kpi;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKpiEvaluatorAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('kpi.evaluator_assignment.update');
    }

    public function rules(): array
    {
        return [
            'effective_end_date' => ['nullable', 'date', 'after_or_equal:effective_start_date'],
            'is_active' => ['boolean'],
        ];
    }
}
