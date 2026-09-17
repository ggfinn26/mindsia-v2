<?php

namespace App\Http\Requests\Finance;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBranchMonthlyCostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('finance.monthly_cost.create');
    }

    public function rules(): array
    {
        return [
            'cost_name' => ['sometimes', 'string', 'max:255'],
            'amount' => ['sometimes', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
