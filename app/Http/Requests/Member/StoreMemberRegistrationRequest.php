<?php

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;

class StoreMemberRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('member.manage');
    }

    public function rules(): array
    {
        return [
            'program_id' => ['required', 'integer', 'exists:programs,id'],
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'receipt_program_name' => ['required', 'string', 'max:255'],
            'original_price' => ['required', 'integer', 'min:0'],
            'discount_code' => ['nullable', 'string', 'exists:discounts,discount_code'],
            'installment_type' => ['required', 'in:1,2,3,4,5'],
        ];
    }
}
