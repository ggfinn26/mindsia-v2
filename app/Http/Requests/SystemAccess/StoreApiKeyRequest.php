<?php

namespace App\Http\Requests\SystemAccess;

use Illuminate\Foundation\Http\FormRequest;

class StoreApiKeyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('system.api_key.create');
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'max:50'],
            'label' => ['required', 'string', 'max:100'],
            'key_value' => ['required', 'string', 'max:500'],
            'is_active' => ['boolean'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
