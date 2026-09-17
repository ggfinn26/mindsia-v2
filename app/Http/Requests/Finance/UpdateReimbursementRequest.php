<?php

namespace App\Http\Requests\Finance;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReimbursementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('finance.reimbursement.create');
    }

    public function rules(): array
    {
        return [
            'branch_id' => ['sometimes', 'exists:branches,id'],
            'period_year' => ['sometimes', 'integer', 'min:2020', 'max:2099'],
            'period_month' => ['sometimes', 'integer', 'min:1', 'max:12'],
            'title' => ['sometimes', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
