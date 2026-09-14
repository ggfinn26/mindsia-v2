<?php

namespace App\Http\Requests\SystemAccess;

use Illuminate\Foundation\Http\FormRequest;

class StoreDashboardWidgetConfigRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('system.permission.manage');
    }

    public function rules(): array
    {
        return [
            'position_id' => ['required', 'exists:positions,id'],
            'widget_key' => ['required', 'string', 'max:100'],
            'order' => ['required', 'integer', 'min:0'],
            'is_enabled' => ['boolean'],
            'custom_settings' => ['nullable', 'array'],
        ];
    }
}
