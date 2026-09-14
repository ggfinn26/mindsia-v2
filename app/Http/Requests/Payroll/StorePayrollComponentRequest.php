<?php

namespace App\Http\Requests\Payroll;

use Illuminate\Foundation\Http\FormRequest;

class StorePayrollComponentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('payroll.component.create');
    }

    public function rules(): array
    {
        return [
            'component_code' => ['required', 'string', 'max:100', 'unique:payroll_components,component_code'],
            'component_name' => ['required', 'string', 'max:255'],
            'component_type' => ['required', 'in:earning,deduction'],
            'calculation_method' => ['required', 'in:fixed,daily,session,percentage,manual'],
            'is_taxable' => ['boolean'],
            'is_active' => ['boolean'],
        ];
    }
}
