<?php

namespace App\Http\Requests\Kpi;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateKpiGradeRuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('kpi.grade_rule.update');
    }

    public function rules(): array
    {
        $id = $this->route('kpiGradeRule')->id;

        return [
            'grade' => ['required', 'string', 'max:10', Rule::unique('kpi_grade_rules', 'grade')->ignore($id)],
            'minimum_score' => ['required', 'numeric', 'min:0', 'max:100', Rule::unique('kpi_grade_rules', 'minimum_score')->ignore($id)],
            'maximum_score' => ['nullable', 'numeric', 'min:0', 'max:100', 'gte:minimum_score'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];
    }
}
