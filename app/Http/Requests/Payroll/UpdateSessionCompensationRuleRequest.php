<?php

namespace App\Http\Requests\Payroll;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSessionCompensationRuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('payroll.session_compensation_rule.update');
    }

    public function rules(): array
    {
        $rule = $this->route('sessionRule');

        return [
            'rule_code' => ['sometimes', 'string', 'max:100', Rule::unique('session_compensation_rules', 'rule_code')->ignore($rule)],
            'rule_name' => ['sometimes', 'string', 'max:255'],
            'scope_type' => ['sometimes', 'in:global,role,position,employee'],
            'role_id' => ['nullable', 'integer', 'exists:roles,id'],
            'position_id' => ['nullable', 'integer', 'exists:positions,id'],
            'employee_id' => ['nullable', 'integer', 'exists:employees,id'],
            'amount_per_session' => ['sometimes', 'numeric', 'min:0'],
            'is_active' => ['boolean'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
