<?php

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;

class StoreMemberSupportTicketReplyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('web')->check() || auth('member')->check();
    }

    public function rules(): array
    {
        return [
            'reply' => ['required', 'string', 'min:5'],
            'employee_id' => ['nullable', 'integer', 'exists:employees,id'],
            'member_id' => ['nullable', 'integer', 'exists:members_data,id'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            if ($this->filled('employee_id') && $this->filled('member_id')) {
                $v->errors()->add('employee_id', 'Hanya satu antara employee_id dan member_id yang boleh diisi.');
            }
        });
    }
}
