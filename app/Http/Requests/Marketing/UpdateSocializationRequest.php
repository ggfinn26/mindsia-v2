<?php

namespace App\Http\Requests\Marketing;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSocializationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('marketing.socialization.update');
    }

    public function rules(): array
    {
        return [
            'location_name' => ['sometimes', 'string', 'max:255'],
            'status' => ['sometimes', 'in:draft,scheduled,completed,cancelled'],
            'partner_fee_amount' => ['nullable', 'numeric', 'min:0'],
            'partner_fee_due_date' => ['nullable', 'date'],
        ];
    }
}
