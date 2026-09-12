<?php

namespace App\Http\Requests\Payroll;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePayrollPeriodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('payroll_period.create');
    }

    public function rules(): array
    {
        return [
            'period_month' => [
                'required',
                'integer',
                'between:1,12',
                Rule::unique('payroll_periods', 'period_month')->where('period_year', $this->period_year),
            ],
            'period_year' => ['required', 'integer', 'min:2020'],
            'pay_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
