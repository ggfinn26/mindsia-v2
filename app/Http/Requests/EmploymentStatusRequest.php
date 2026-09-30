<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmploymentStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('contract.manage');
    }

    public function rules(): array
    {
        $tab = $this->input('tab', 'lengkap');
        $common = [
            'tab' => ['required', 'in:lengkap,cepat'],
            'type_employment' => ['required', 'string', 'in:Tetap,Kontrak,Magang'],
            'join_date' => ['required', 'date'],
            'position_id' => ['required', 'exists:positions,id'],
        ];

        if ($tab === 'cepat') {
            return array_merge($common, [
                'contract_end_date' => ['nullable', 'date'],
                'contract_file_path' => ['required', 'file', 'mimes:pdf', 'max:10240'],
            ]);
        }

        return array_merge($common, [
            'contract_start_date' => ['required', 'date'],
            'contract_end_date' => ['nullable', 'date', 'after_or_equal:contract_start_date'],
        ]);
    }

    public function messages(): array
    {
        return [
            'type_employment.required' => 'Jenis ketenagakerjaan wajib dipilih.',
            'type_employment.in' => 'Jenis ketenagakerjaan harus salah satu dari: Tetap, Kontrak, Magang.',
            'join_date.required' => 'Tanggal mulai kerja wajib diisi.',
            'join_date.date' => 'Tanggal mulai kerja tidak valid.',
            'position_id.required' => 'Posisi/jabatan wajib dipilih.',
            'position_id.exists' => 'Posisi yang dipilih tidak ditemukan.',
            'contract_start_date.required' => 'Tanggal mulai kontrak wajib diisi.',
            'contract_start_date.date' => 'Tanggal mulai kontrak tidak valid.',
            'contract_end_date.date' => 'Tanggal akhir kontrak tidak valid.',
            'contract_end_date.after_or_equal' => 'Tanggal akhir kontrak harus sama dengan atau setelah tanggal mulai kontrak.',
            'contract_file_path.required' => 'File kontrak wajib diupload.',
            'contract_file_path.mimes' => 'File kontrak harus berformat PDF.',
            'contract_file_path.max' => 'Ukuran file kontrak maksimal 10MB.',
        ];
    }
}
