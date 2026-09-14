<?php

namespace App\Http\Requests\Facility;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBranchRentContractRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('facility.rent_contract.update');
    }

    public function rules(): array
    {
        return [
            'owner_name' => ['sometimes', 'string', 'max:150'],
            'owner_phone' => ['nullable', 'string', 'max:30'],
            'status' => ['sometimes', 'in:active,expired,terminated'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
