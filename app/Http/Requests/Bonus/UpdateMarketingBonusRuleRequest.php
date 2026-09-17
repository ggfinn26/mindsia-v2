<?php

namespace App\Http\Requests\Bonus;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateMarketingBonusRuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('bonus.marketing-rule.update');
    }

    public function rules(): array
    {
        $id = $this->route('marketingRule')?->id;

        return [
            'rule_code' => ['required', 'string', 'max:100', "unique:marketing_bonus_rules,rule_code,{$id}"],
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

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($v) {
            $scopeType = $this->input('scope_type');
            $scopeFieldMap = ['role' => 'role_id', 'position' => 'position_id', 'employee' => 'employee_id'];

            foreach ($scopeFieldMap as $scope => $field) {
                if ($scopeType !== $scope && $this->filled($field)) {
                    $v->errors()->add($field, "Field {$field} harus kosong jika scope_type bukan '{$scope}'.");
                }
                if ($scopeType === $scope && ! $this->filled($field)) {
                    $v->errors()->add($field, "Field {$field} wajib diisi jika scope_type adalah '{$scope}'.");
                }
            }
        });
    }
}
