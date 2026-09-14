<?php

namespace App\Http\Requests\Notification;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreNotificationTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('notification.template.create');
    }

    public function rules(): array
    {
        return [
            'template_key' => ['required', 'string', 'max:100', 'unique:notification_templates,template_key'],
            'type' => ['required', 'in:email,telegram,in-app'],
            'subject' => ['nullable', 'string', 'max:255'],
            'body' => ['required', 'string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($v) {
            if ($this->input('type') === 'email' && ! $this->input('subject')) {
                $v->errors()->add('subject', 'Subject wajib diisi untuk template email.');
            }
        });
    }
}
