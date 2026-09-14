<?php

namespace App\Http\Requests\Facility;

use Illuminate\Foundation\Http\FormRequest;

class AdjustInventoryQuantityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('facility.inventory.update');
    }

    public function rules(): array
    {
        return [
            'quantity' => ['required', 'integer', 'min:0'],
            'reason' => ['required', 'string'],
        ];
    }
}
