<?php

namespace App\Http\Requests\SystemAccess;

use Illuminate\Foundation\Http\FormRequest;

class StoreBotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('system.bot.manage');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', 'in:telegram,whatsapp'],
            'token' => ['required', 'string', 'max:500'],
            'is_active' => ['boolean'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
