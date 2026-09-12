<?php

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;

class StoreMemberSupportTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('web')->check() || auth('member')->check();
    }

    public function rules(): array
    {
        return [
            'member_id' => ['required', 'integer', 'exists:members_data,id'],
            'branch_id' => ['required', 'integer', 'exists:branches,id'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'category' => ['required', 'in:complaint,suggestion,question'],
            'priority' => ['required', 'in:low,medium,high'],
            'telegram_attachment_id' => ['nullable', 'string', 'max:255'],
        ];
    }
}
