<?php

namespace App\Http\Requests\SystemAccess;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('system.bot.manage');
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:100'],
            'type' => ['sometimes', 'in:telegram,whatsapp'],
            'token' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
