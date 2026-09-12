<?php

namespace App\Http\Requests\Payroll;

use Illuminate\Foundation\Http\FormRequest;

class ProcessPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('payroll.process_payment');
    }

    public function rules(): array
    {
        return [
            'payment_method' => ['required', 'in:bank_transfer,cash,other'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'paid_at' => ['nullable', 'date'],
            'payment_reference' => ['nullable', 'string', 'max:255'],
            'telegram_proof_id' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
