<?php

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;

class ChangeMemberSupportTicketStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(['BOARD_OF_DIRECTORS', 'MANAGER_AREA']);
    }

    public function rules(): array
    {
        return [
            'new_status' => ['required', 'in:open,verified,resolved,rejected'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
