<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBranchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('organization.branch.create');
    }

    public function rules(): array
    {
        return [
            'areas_id' => ['required', 'exists:areas,id'],
            'branch_name' => ['required', 'string', 'max:150'],
            'code_branches' => ['required', 'string', 'max:20', 'unique:branches,code_branches'],
            'address' => ['required', 'string'],
            'gmaps_url' => ['nullable', 'string', 'max:500'],
            'whatsapp' => ['required', 'string', 'min:8', 'max:15'],
            'instagram' => ['nullable', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'radius_meters' => ['required', 'integer', 'min:1'],
            'ma_pic_employee_id' => ['nullable', 'exists:employees,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'areas_id.required' => 'Area wajib dipilih',
            'areas_id.exists' => 'Area tidak valid',
            'branch_name.required' => 'Nama cabang wajib diisi',
            'branch_name.max' => 'Nama cabang maksimal 150 karakter',
            'code_branches.required' => 'Kode cabang wajib diisi',
            'code_branches.max' => 'Kode cabang maksimal 20 karakter',
            'code_branches.unique' => 'Kode cabang sudah digunakan',
            'address.required' => 'Alamat wajib diisi',
            'whatsapp.required' => 'Nomor WhatsApp wajib diisi',
            'whatsapp.min' => 'Nomor WhatsApp minimal 8 digit',
            'whatsapp.max' => 'Nomor WhatsApp maksimal 15 digit',
            'latitude.required' => 'Latitude wajib diisi',
            'latitude.numeric' => 'Latitude harus berupa angka',
            'latitude.between' => 'Latitude harus antara -90 dan 90',
            'longitude.required' => 'Longitude wajib diisi',
            'longitude.numeric' => 'Longitude harus berupa angka',
            'longitude.between' => 'Longitude harus antara -180 dan 180',
            'radius_meters.required' => 'Radius wajib diisi',
            'radius_meters.integer' => 'Radius harus berupa angka bulat',
            'radius_meters.min' => 'Radius minimal 1 meter',
            'ma_pic_employee_id.exists' => 'Pegawai PIC tidak valid',
        ];
    }
}
