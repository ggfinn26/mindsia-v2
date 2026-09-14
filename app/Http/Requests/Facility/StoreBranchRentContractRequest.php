<?php

namespace App\Http\Requests\Facility;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreBranchRentContractRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('facility.rent_contract.create');
    }

    public function rules(): array
    {
        return [
            'branch_id' => ['required', 'exists:branches,id'],
            'area_id' => ['nullable', 'exists:areas,id'],
            'owner_name' => ['required', 'string', 'max:150'],
            'owner_phone' => ['nullable', 'string', 'max:30'],
            'rent_amount' => ['required', 'numeric', 'min:0'],
            'down_payment' => ['required', 'numeric', 'min:0', 'lt:rent_amount'],
            'termin_count' => ['required', 'integer', 'min:1'],
            'rent_period' => ['required', 'in:monthly,yearly,one_time'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'gte:start_date'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            if ($this->input('rent_period') === 'one_time' && (int) $this->input('termin_count') !== 1) {
                $v->errors()->add('termin_count', 'one_time hanya boleh 1 termin.');
            }
        });
    }
}
