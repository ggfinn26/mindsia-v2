<?php

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreMemberSupportTicketReplyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('web')->check() || auth('member')->check();
    }

    public function rules(): array
    {
        return [
            'reply' => ['required', 'string'],
            'employee_id' => ['nullable', 'integer', 'exists:employees,id'],
            'member_id' => ['nullable', 'integer', 'exists:members_data,id'],
            'telegram_attachment_id' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($v) {
            $hasEmployee = (bool) $this->input('employee_id');
            $hasMember = (bool) $this->input('member_id');

            if ($hasEmployee === $hasMember) {
                $v->errors()->add('employee_id', 'Tepat satu dari employee_id atau member_id harus diisi.');
            }
        });
    }
}
