<?php

namespace App\Http\Requests;

use App\Models\Employee;
use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Employee::class);
    }

    public function rules(): array
    {
        return [
            'employee_code' => ['required', 'string', 'unique:employees'],
            'full_name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:L,P'],
            'birthdate' => ['required', 'date', 'before_or_equal:'.now()->subYears(17)->toDateString()],
            'join_date' => ['nullable', 'date'],
            'email' => ['required', 'email', 'unique:employees'],
            'whatsapp_number' => ['required', 'string', 'min:8', 'max:15'],
            'branch_id' => ['required', 'exists:branches,id'],
            'region_id' => ['required_unless:is_hq,1', 'exists:regions,id'],
            'area_id' => ['nullable', 'exists:areas,id'],
            'is_hq' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'employee_code.required' => 'Kode pegawai wajib diisi.',
            'employee_code.unique' => 'Kode pegawai sudah digunakan.',
            'full_name.required' => 'Nama lengkap wajib diisi.',
            'gender.required' => 'Jenis kelamin wajib dipilih.',
            'gender.in' => 'Jenis kelamin tidak valid.',
            'birthdate.required' => 'Tanggal lahir wajib diisi.',
            'birthdate.date' => 'Tanggal lahir tidak valid.',
            'birthdate.before_or_equal' => 'Pegawai harus berusia minimal 17 tahun.',
            'join_date.date' => 'Tanggal mulai kerja tidak valid.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'whatsapp_number.required' => 'Nomor WhatsApp wajib diisi.',
            'whatsapp_number.min' => 'Nomor WhatsApp minimal 8 digit.',
            'whatsapp_number.max' => 'Nomor WhatsApp maksimal 15 digit.',
            'region_id.required_unless' => 'Region wajib dipilih jika bukan pegawai HQ.',
        ];
    }
}
