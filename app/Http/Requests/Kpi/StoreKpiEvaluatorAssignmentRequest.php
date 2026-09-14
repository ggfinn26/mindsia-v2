<?php

namespace App\Http\Requests\Kpi;

use App\Models\KpiEvaluatorAssignment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreKpiEvaluatorAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('kpi.evaluator_assignment.create');
    }

    public function rules(): array
    {
        return [
            'evaluator_employee_id' => ['required', 'exists:employees,id', 'different:evaluatee_employee_id'],
            'evaluatee_employee_id' => ['required', 'exists:employees,id'],
            'kpi_template_id' => ['nullable', 'exists:kpi_templates,id'],
            'effective_start_date' => ['required', 'date'],
            'effective_end_date' => ['nullable', 'date', 'after_or_equal:effective_start_date'],
            'is_active' => ['boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($v) {
            $exists = KpiEvaluatorAssignment::where('evaluator_employee_id', $this->input('evaluator_employee_id'))
                ->where('evaluatee_employee_id', $this->input('evaluatee_employee_id'))
                ->where('kpi_template_id', $this->input('kpi_template_id'))
                ->where('is_active', true)
                ->exists();

            if ($exists) {
                $v->errors()->add('evaluatee_employee_id', 'Assignment evaluator aktif untuk evaluatee dan template ini sudah ada.');
            }
        });
    }
}
