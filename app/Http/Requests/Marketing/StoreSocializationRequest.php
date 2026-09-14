<?php

namespace App\Http\Requests\Marketing;

use Illuminate\Foundation\Http\FormRequest;

class StoreSocializationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('marketing.socialization.create');
    }

    public function rules(): array
    {
        return [
            'branch_id' => ['required', 'exists:branches,id'],
            'area_id' => ['nullable', 'exists:areas,id'],
            'institution_id' => ['nullable', 'exists:institutions,id'],
            'location_name' => ['required', 'string', 'max:255'],
            'partner_fee_amount' => ['nullable', 'numeric', 'min:0'],
            'partner_fee_due_date' => ['nullable', 'date', 'required_with:partner_fee_amount'],
        ];
    }
}
