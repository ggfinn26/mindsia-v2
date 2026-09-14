<?php

namespace App\Http\Requests\SystemAccess;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDashboardWidgetConfigRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('system.permission.manage');
    }

    public function rules(): array
    {
        return [
            'order' => ['sometimes', 'integer', 'min:0'],
            'is_enabled' => ['boolean'],
            'custom_settings' => ['nullable', 'array'],
        ];
    }
}
