<?php

namespace App\Http\Requests\Notification;

use Illuminate\Foundation\Http\FormRequest;

class StoreNotificationVariableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('notification.variable.create');
    }

    public function rules(): array
    {
        return [
            'variable_name' => ['required', 'string', 'max:100'],
            'variable_type' => ['required', 'string', 'max:50'],
            'variable_description' => ['nullable', 'string'],
        ];
    }
}
