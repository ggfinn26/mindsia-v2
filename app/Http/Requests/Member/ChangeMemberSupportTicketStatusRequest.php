<?php

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;

class ChangeMemberSupportTicketStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('member.support.manage');
    }

    public function rules(): array
    {
        return [
            'new_status' => ['required', 'in:open,in_progress,resolved,closed'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
