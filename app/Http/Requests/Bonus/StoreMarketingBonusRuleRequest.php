<?php

namespace App\Http\Requests\Bonus;

use Illuminate\Foundation\Http\FormRequest;

class StoreMarketingBonusRuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('BOARD_OF_DIRECTORS');
    }

    public function rules(): array
    {
        return [
            'rule_code' => ['required', 'string', 'max:100', 'unique:marketing_bonus_rules,rule_code'],
            'rule_name' => ['required', 'string', 'max:255'],
            'scope_type' => ['required', 'in:global,role,position,employee'],
            'role_id' => ['nullable', 'integer', 'exists:roles,id'],
            'position_id' => ['nullable', 'integer', 'exists:positions,id'],
            'employee_id' => ['nullable', 'integer', 'exists:employees,id'],
            'bonus_basis' => ['required', 'in:marketing_mpi,revenue'],
            'revenue_basis' => ['nullable', 'string', 'max:100'],
            'reward_basis' => ['required', 'in:base_salary,fixed'],
            'is_active' => ['boolean'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
