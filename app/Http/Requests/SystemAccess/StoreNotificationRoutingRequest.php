<?php

namespace App\Http\Requests\SystemAccess;

use Illuminate\Foundation\Http\FormRequest;

class StoreNotificationRoutingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('system.notification_routing.manage');
    }

    public function rules(): array
    {
        return [
            'event_key' => ['required', 'string', 'max:100'],
            'position_id' => ['nullable', 'exists:positions,id'],
            'channel' => ['required', 'in:in-app,email,telegram'],
            'scope' => ['required', 'in:global,branch,area'],
            'is_active' => ['boolean'],
            'description' => ['nullable', 'string'],
        ];
    }
}
