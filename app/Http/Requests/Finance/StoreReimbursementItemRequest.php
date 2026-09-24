<?php

namespace App\Http\Requests\Finance;

use Illuminate\Foundation\Http\FormRequest;

class StoreReimbursementItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('finance.reimbursement.create');
    }

    public function rules(): array
    {
        return [
            'expense_date' => ['required', 'date'],
            'category' => ['required', 'in:transport,makan,akomodasi,perlengkapan,lainnya'],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
