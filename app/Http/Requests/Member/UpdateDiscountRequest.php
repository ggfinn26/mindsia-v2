<?php

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDiscountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('member.manage');
    }

    public function rules(): array
    {
        $id = $this->route('discount')?->id;

        return [
            'discount_code' => ['required', 'string', 'max:50', "unique:discounts,discount_code,{$id}"],
            'discount_type' => ['required', 'in:percentage,fixed_amount'],
            'discount_nominal' => ['required', 'integer', 'min:0', $this->discount_type === 'percentage' ? 'max:100' : 'max:4294967295'],
            'discount_quota' => ['nullable', 'integer', 'min:0'],
            'starts_at' => ['nullable', 'date'],
            'expired_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ];
    }

    public function messages(): array
    {
        return [
            'discount_code.required' => 'Kode diskon wajib diisi.',
            'discount_code.unique' => 'Kode diskon sudah digunakan.',
            'discount_code.max' => 'Kode diskon maksimal 50 karakter.',
            'discount_type.required' => 'Tipe diskon wajib dipilih.',
            'discount_type.in' => 'Tipe diskon tidak valid.',
            'discount_nominal.required' => 'Nilai diskon wajib diisi.',
            'discount_nominal.min' => 'Nilai diskon tidak boleh negatif.',
            'discount_quota.min' => 'Kuota tidak boleh negatif.',
            'expired_at.after_or_equal' => 'Tanggal berakhir harus setelah atau sama dengan tanggal mulai.',
        ];
    }
}
