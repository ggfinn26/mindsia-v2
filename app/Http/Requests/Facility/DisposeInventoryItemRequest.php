<?php

namespace App\Http\Requests\Facility;

use App\Models\InventoryItem;
use Illuminate\Foundation\Http\FormRequest;

class DisposeInventoryItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var InventoryItem $item */
        $item = $this->route('inventoryItem');

        return $item->isActive() && $this->user()->can('facility.inventory.dispose');
    }

    public function rules(): array
    {
        return [
            'reason' => ['required', 'string'],
        ];
    }
}
