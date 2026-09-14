<?php

namespace App\Http\Requests\Payroll;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeCompensationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('payroll.employee_compensation.update');
    }

    public function rules(): array
    {
        return [
            'value' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
