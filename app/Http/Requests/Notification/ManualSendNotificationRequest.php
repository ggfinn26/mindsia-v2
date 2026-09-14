<?php

namespace App\Http\Requests\Notification;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Validator;

class ManualSendNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('notification.send_manual');
    }

    public function rules(): array
    {
        return [
            'template_key' => ['required', 'string', 'exists:notification_templates,template_key'],
            'recipient_type' => ['required', 'in:employee,member'],
            'recipient_id' => ['required', 'integer', 'min:1'],
            'payload' => ['nullable', 'array'],
            'payload.*' => ['nullable', 'string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($v) {
            $type = $this->input('recipient_type');
            $id = $this->input('recipient_id');
            $table = $type === 'employee' ? 'employees' : 'members_data';

            if ($id && ! DB::table($table)->where('id', $id)->exists()) {
                $v->errors()->add('recipient_id', ucfirst($type).' tidak ditemukan.');
            }
        });
    }
}
