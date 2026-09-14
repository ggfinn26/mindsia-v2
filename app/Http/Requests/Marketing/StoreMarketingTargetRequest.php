<?php

namespace App\Http\Requests\Marketing;

use Illuminate\Foundation\Http\FormRequest;

class StoreMarketingTargetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('marketing.target.set');
    }

    public function rules(): array
    {
        return [
            'employee_id' => ['required', 'exists:employees,id'],
            'period_month' => ['required', 'integer', 'min:1', 'max:12'],
            'period_year' => ['required', 'integer', 'min:2020', 'max:2100'],
            'classes_target' => ['required', 'integer', 'min:0'],
            'omzet_target' => ['required', 'numeric', 'min:0'],
        ];
    }
}
