<?php

namespace App\Http\Requests\Payroll;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeCompensationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('employee_compensation.create');
    }

    public function rules(): array
    {
        return [
            'payroll_component_id' => ['required', 'integer', 'exists:payroll_components,id'],
            'value' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
