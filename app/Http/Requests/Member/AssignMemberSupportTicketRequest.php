<?php

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;

class AssignMemberSupportTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('member.support.manage');
    }

    public function rules(): array
    {
        return [
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
        ];
    }
}
