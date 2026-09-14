<?php

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;

class SyncDiscountProgramsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('member.manage');
    }

    public function rules(): array
    {
        return [
            'program_ids' => ['nullable', 'array'],
            'program_ids.*' => ['integer', 'exists:programs,id'],
        ];
    }
}
