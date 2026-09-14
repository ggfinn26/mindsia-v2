<?php

namespace App\Http\Requests\Payroll;

use Illuminate\Foundation\Http\FormRequest;

class AdjustPayrollItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('payroll.item.adjust');
    }

    public function rules(): array
    {
        return [
            'payroll_item_id' => ['nullable', 'integer', 'exists:payroll_items,id'],
            'adjustment_type' => ['required', 'in:earning,deduction,correction'],
            'new_amount' => ['required', 'numeric', 'min:0'],
            'adjustment_reason' => ['required', 'string', 'min:5'],
        ];
    }
}
