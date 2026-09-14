<?php

namespace App\Http\Requests\Bonus;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKpiBonusRuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('bonus.kpi-rule.update');
    }

    public function rules(): array
    {
        $id = $this->route('kpiRule')?->id;

        return [
            'rule_code' => ['required', 'string', 'max:100', "unique:kpi_bonus_rules,rule_code,{$id}"],
            'rule_name' => ['required', 'string', 'max:255'],
            'scope_type' => ['required', 'in:global,role,position,employee'],
            'role_id' => ['nullable', 'integer', 'exists:roles,id'],
            'position_id' => ['nullable', 'integer', 'exists:positions,id'],
            'employee_id' => ['nullable', 'integer', 'exists:employees,id'],
            'reward_basis' => ['required', 'in:base_salary,fixed'],
            'is_active' => ['boolean'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
