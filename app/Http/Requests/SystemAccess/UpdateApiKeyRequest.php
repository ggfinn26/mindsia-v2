<?php

namespace App\Http\Requests\SystemAccess;

use Illuminate\Foundation\Http\FormRequest;

class UpdateApiKeyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('system.api_key.update');
    }

    public function rules(): array
    {
        return [
            'label' => ['sometimes', 'string', 'max:100'],
            'key_value' => ['sometimes', 'string', 'max:500'],
            'is_active' => ['boolean'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
