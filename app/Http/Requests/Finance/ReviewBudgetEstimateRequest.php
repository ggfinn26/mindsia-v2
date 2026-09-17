<?php

namespace App\Http\Requests\Finance;

use Illuminate\Foundation\Http\FormRequest;

class ReviewBudgetEstimateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('finance.budget_estimate.ops_review');
    }

    public function rules(): array
    {
        return [
            'action' => ['required', 'in:accept,reject'],
            'rejection_notes' => ['required_if:action,reject', 'string', 'max:1000'],
        ];
    }
}
