<?php

namespace App\Http\Requests\Payroll;

use Illuminate\Foundation\Http\FormRequest;

class GenerateSlipRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('payroll.slip.generate');
    }

    public function rules(): array
    {
        return [
            'signatory_employee_id' => ['nullable', 'integer', 'exists:employees,id'],
        ];
    }
}
