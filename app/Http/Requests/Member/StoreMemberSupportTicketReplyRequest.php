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
        ];
    }
}
