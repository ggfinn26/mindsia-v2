<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAreaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('organization.area.create');
    }

    public function rules(): array
    {
        return [
            'region_id' => ['required', 'exists:regions,id'],
            'name' => ['required', 'string', 'max:100', 'unique:areas,name'],
        ];
    }
}
