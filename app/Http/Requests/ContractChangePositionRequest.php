<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContractChangePositionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('contract.manage');
    }

    public function rules(): array
    {
        return [
            'position_id' => ['required', 'exists:positions,id', 'different:current_position_id'],
            'contract_start_date' => ['required', 'date'],
            'contract_end_date' => ['nullable', 'date', 'after_or_equal:contract_start_date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'position_id.required' => 'Posisi baru wajib dipilih.',
            'position_id.exists' => 'Posisi yang dipilih tidak ditemukan.',
            'position_id.different' => 'Posisi baru harus berbeda dari posisi saat ini.',
            'contract_start_date.required' => 'Tanggal mulai kontrak wajib diisi.',
            'contract_start_date.date' => 'Tanggal mulai kontrak tidak valid.',
            'contract_end_date.date' => 'Tanggal akhir kontrak tidak valid.',
            'contract_end_date.after_or_equal' => 'Tanggal akhir kontrak harus sama dengan atau setelah tanggal mulai kontrak.',
        ];
    }
}
