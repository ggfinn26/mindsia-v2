<?php

namespace App\Http\Requests\SystemAccess;

use App\Http\Controllers\Admin\SystemAccess\WebhookStatusController;
use Illuminate\Foundation\Http\FormRequest;

class StoreWebhookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('system.webhook.manage');
    }

    public function rules(): array
    {
        return [
            'bot_id' => ['nullable', 'exists:bots,id'],
            'name' => ['required', 'string', 'max:100'],
            'endpoint_url' => ['required', 'url', 'max:500', $this->safeUrlRule()],
            'notes' => ['nullable', 'string'],
        ];
    }

    private function safeUrlRule(): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail) {
            try {
                WebhookStatusController::assertSafeUrl($value);
            } catch (\Throwable $e) {
                $fail('URL tidak diizinkan: '.$e->getMessage());
            }
        };
    }
}
