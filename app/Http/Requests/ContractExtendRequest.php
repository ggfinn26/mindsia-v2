<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContractExtendRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('contract.manage');
    }

    public function rules(): array
    {
        return [
            'new_end_date' => ['required', 'date', 'after:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'new_end_date.required' => 'Tanggal akhir kontrak baru wajib diisi.',
            'new_end_date.date' => 'Tanggal akhir kontrak baru harus berupa tanggal yang valid.',
            'new_end_date.after' => 'Tanggal akhir kontrak baru harus setelah hari ini.',
        ];
    }
}
