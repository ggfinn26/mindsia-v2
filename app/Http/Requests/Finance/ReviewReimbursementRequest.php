<?php

namespace App\Http\Requests\Finance;

use Illuminate\Foundation\Http\FormRequest;

class ReviewReimbursementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('finance.reimbursement.review');
    }

    public function rules(): array
    {
        return [
            'action' => ['required', 'in:approve,reject'],
            'review_notes' => ['nullable', 'string', 'max:1000'],
            'rejection_notes' => ['required_if:action,reject', 'string', 'max:1000'],
        ];
    }
}
