<?php

namespace App\Http\Requests\Payroll;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdatePayrollComponentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('payroll.component.update');
    }

    public function rules(): array
    {
        $component = $this->route('component');

        return [
            'component_code' => ['sometimes', 'string', 'max:100', Rule::unique('payroll_components', 'component_code')->ignore($component)],
            'component_name' => ['sometimes', 'string', 'max:255'],
            'component_type' => ['sometimes', 'in:earning,deduction'],
            'calculation_method' => ['sometimes', 'in:fixed,daily,session,percentage,manual'],
            'is_taxable' => ['boolean'],
            'is_active' => ['boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($v) {
            $component = $this->route('component');
            if ($this->has('calculation_method') && $component?->payrollItems()->exists()) {
                $v->errors()->add('calculation_method', 'Metode kalkulasi tidak bisa diubah karena komponen sudah digunakan dalam payroll.');
            }
        });
    }
}
