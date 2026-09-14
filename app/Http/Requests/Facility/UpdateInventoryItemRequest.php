<?php

namespace App\Http\Requests\Facility;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInventoryItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('facility.inventory.update');
    }

    public function rules(): array
    {
        return [
            'item_name' => ['sometimes', 'string', 'max:200'],
            'category' => ['nullable', 'string', 'max:100'],
            'inventory_type' => ['sometimes', 'in:FIXED_ASSET,SUPPLIES,OTHER'],
            'unit' => ['sometimes', 'string', 'max:50'],
            'purchase_date' => ['nullable', 'date'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
            'input_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
