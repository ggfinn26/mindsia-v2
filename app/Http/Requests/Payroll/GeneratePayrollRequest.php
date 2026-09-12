<?php

namespace App\Http\Requests\Payroll;

use Illuminate\Foundation\Http\FormRequest;

class GeneratePayrollRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('payroll.generate');
    }

    public function rules(): array
    {
        return [];
    }
}
