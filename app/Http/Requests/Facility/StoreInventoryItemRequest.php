<?php

namespace App\Http\Requests\Facility;

use Illuminate\Foundation\Http\FormRequest;

class StoreInventoryItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('facility.inventory.create');
    }

    public function rules(): array
    {
        return [
            'branch_id' => ['required', 'exists:branches,id'],
            'budget_estimate_item_id' => ['nullable', 'exists:budget_estimate_items,id'],
            'item_code' => ['required', 'string', 'max:50'],
            'item_name' => ['required', 'string', 'max:200'],
            'category' => ['nullable', 'string', 'max:100'],
            'inventory_type' => ['required', 'in:FIXED_ASSET,SUPPLIES,OTHER'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit' => ['required', 'string', 'max:50'],
            'condition_status' => ['required', 'in:GOOD,NEEDS_REPAIR,DAMAGED,UNUSABLE'],
            'purchase_date' => ['nullable', 'date'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
            'input_date' => ['nullable', 'date'],
            'location' => ['nullable', 'string', 'max:150'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
