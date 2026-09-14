<?php

namespace App\Http\Requests\Payroll;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePayrollPeriodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('payroll.period.update');
    }

    public function rules(): array
    {
        return [
            'pay_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
