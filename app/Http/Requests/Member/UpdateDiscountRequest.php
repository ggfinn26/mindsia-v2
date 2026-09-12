<?php

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDiscountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('BOARD_OF_DIRECTORS');
    }

    public function rules(): array
    {
        $id = $this->route('discount')?->id;

        return [
            'discount_code' => ['required', 'string', 'max:50', "unique:discounts,discount_code,{$id}"],
            'discount_type' => ['required', 'in:percentage,fixed_amount'],
            'discount_nominal' => ['required', 'integer', 'min:0'],
            'discount_quota' => ['nullable', 'integer', 'min:0'],
            'starts_at' => ['nullable', 'date'],
            'expired_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ];
    }
}
