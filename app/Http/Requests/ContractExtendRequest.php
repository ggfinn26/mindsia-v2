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
            'proposed_end_date' => ['required', 'date', 'after:today'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'proposed_end_date.required' => 'Tanggal akhir kontrak baru wajib diisi.',
            'proposed_end_date.date' => 'Tanggal akhir kontrak baru harus berupa tanggal yang valid.',
            'proposed_end_date.after' => 'Tanggal akhir kontrak baru harus setelah hari ini.',
        ];
    }
}
