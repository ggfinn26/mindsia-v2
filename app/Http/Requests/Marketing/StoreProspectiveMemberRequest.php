<?php

namespace App\Http\Requests\Marketing;

use Illuminate\Foundation\Http\FormRequest;

class StoreProspectiveMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('marketing.prospective_member.create');
    }

    public function rules(): array
    {
        return [
            'socialization_id' => ['nullable', 'exists:socializations,id'],
            'full_name' => ['required', 'string', 'max:255'],
            'whatsapp_number' => ['required', 'string', 'max:50'],
            'instagram' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'gender' => ['nullable', 'in:L,P'],
            'institution_id' => ['nullable', 'exists:institutions,id'],
            'institution_name' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
