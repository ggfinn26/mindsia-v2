<?php

namespace App\Http\Requests\Marketing;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProspectiveMemberStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('marketing.prospective_member.update');
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:ALMOST,YES,NO,FIXED'],
            'change_reason' => ['nullable', 'string'],
        ];
    }
}
