<?php

namespace App\Http\Requests\Kpi;

use Illuminate\Foundation\Http\FormRequest;

class StoreKpiGradeRuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('kpi.grade_rule.create');
    }

    public function rules(): array
    {
        return [
            'grade' => ['required', 'string', 'max:10', 'unique:kpi_grade_rules,grade'],
            'minimum_score' => ['required', 'numeric', 'min:0', 'max:100', 'unique:kpi_grade_rules,minimum_score'],
            'maximum_score' => ['nullable', 'numeric', 'min:0', 'max:100', 'gte:minimum_score'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];
    }
}
