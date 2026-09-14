<?php

namespace App\Http\Requests\Payroll;

use Illuminate\Foundation\Http\FormRequest;

class MarkPaymentFailedRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('payroll.period.pay');
    }

    public function rules(): array
    {
        return [
            'failure_reason' => ['required', 'string', 'max:500'],
        ];
    }
}
