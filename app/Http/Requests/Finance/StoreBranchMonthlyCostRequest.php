<?php

namespace App\Http\Requests\Finance;

use Illuminate\Foundation\Http\FormRequest;

class StoreBranchMonthlyCostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('finance.monthly_cost.create');
    }

    public function rules(): array
    {
        return [
            'branch_id' => ['required', 'exists:branches,id'],
            'period_year' => ['required', 'integer', 'min:2020', 'max:2099'],
            'period_month' => ['required', 'integer', 'min:1', 'max:12'],
            'category' => ['required', 'in:sewa,listrik,air,internet,gaji_non_employee,peralatan,kebersihan,keamanan,lainnya'],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
