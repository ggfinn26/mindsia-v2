<?php

namespace App\Http\Requests\SystemAccess;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNotificationRoutingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('system.notification_routing.manage');
    }

    public function rules(): array
    {
        return [
            'event_key' => ['sometimes', 'string', 'max:100'],
            'position_id' => ['nullable', 'exists:positions,id'],
            'channel' => ['sometimes', 'in:in-app,email,telegram'],
            'scope' => ['sometimes', 'in:global,branch,area'],
            'is_active' => ['boolean'],
            'description' => ['nullable', 'string'],
        ];
    }
}
