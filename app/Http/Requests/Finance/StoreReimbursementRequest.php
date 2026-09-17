<?php

namespace App\Http\Requests\Finance;

use Illuminate\Foundation\Http\FormRequest;

class StoreReimbursementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('finance.reimbursement.create');
    }

    public function rules(): array
    {
        return [
            'branch_id' => ['required', 'exists:branches,id'],
            'period_year' => ['required', 'integer', 'min:2020', 'max:2099'],
            'period_month' => ['required', 'integer', 'min:1', 'max:12'],
            'title' => ['required', 'string', 'max:255'],
            'expense_period_start' => ['required', 'date'],
            'expense_period_end' => ['required', 'date', 'after_or_equal:expense_period_start'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
