<?php

namespace App\Http\Requests\Kpi;

use App\Models\KpiEvaluatorAssignment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreKpiEvaluationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if (! $user->can('kpi.evaluation.create')) {
            return false;
        }

        $evaluatorEmployee = $user->employee;
        if (! $evaluatorEmployee) {
            return false;
        }

        return KpiEvaluatorAssignment::active()
            ->effective()
            ->where('evaluator_employee_id', $evaluatorEmployee->id)
            ->where('evaluatee_employee_id', $this->input('employee_id'))
            ->where(fn ($q) => $q
                ->whereNull('kpi_template_id')
                ->orWhere('kpi_template_id', $this->input('kpi_template_id'))
            )
            ->exists();
    }

    public function rules(): array
    {
        return [
            'employee_id' => [
                'required',
                'exists:employees,id',
                Rule::unique('employee_kpi_evaluations')->where(function ($query) {
                    return $query->where('kpi_template_id', $this->kpi_template_id)
                        ->where('period_type', $this->period_type)
                        ->where('period_start_date', $this->period_start_date)
                        ->where('period_end_date', $this->period_end_date);
                }),
            ],
            'kpi_template_id' => ['required', 'exists:kpi_templates,id'],
            'period_type' => ['required', 'in:monthly,quarterly,yearly'],
            'period_start_date' => ['required', 'date'],
            'period_end_date' => ['required', 'date', 'after_or_equal:period_start_date'],
        ];
    }

    public function messages(): array
    {
        return [
            'employee_id.unique' => 'Evaluasi KPI untuk pegawai, template, dan periode ini sudah pernah dibuat.',
        ];
    }
}
