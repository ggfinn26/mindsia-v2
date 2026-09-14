<?php

namespace App\Http\Requests\Notification;

use Illuminate\Foundation\Http\FormRequest;

class ResendNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('notification.log.resend');
    }

    public function rules(): array
    {
        return [];
    }
}
