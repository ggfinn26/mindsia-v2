<?php

namespace App\Http\Requests\Marketing;

use App\Models\Branch;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSocializationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('marketing.socialization.create');
    }

    public function rules(): array
    {
        $employee = $this->user()->employee;

        // Branch-level employee: branch_id fixed from profile — no form input needed.
        // Area/region/HQ employees submit branch_id from form — must be within their scope.
        $allowedBranchIds = match (true) {
            (bool) $employee->branch_id => [$employee->branch_id],
            (bool) $employee->area_id => Branch::where('areas_id', $employee->area_id)->pluck('id')->all(),
            (bool) $employee->region_id => Branch::whereHas('area', fn ($q) => $q->where('region_id', $employee->region_id))->pluck('id')->all(),
            default => Branch::pluck('id')->all(),
        };

        return [
            'branch_id' => ['nullable', Rule::in($allowedBranchIds)],
            'institution_id' => ['nullable', 'exists:institutions,id'],
            'location_name' => ['required', 'string', 'max:255'],
            'partner_fee_amount' => ['nullable', 'numeric', 'min:0'],
            'partner_fee_due_date' => ['nullable', 'date', 'required_with:partner_fee_amount'],
        ];
    }
}
