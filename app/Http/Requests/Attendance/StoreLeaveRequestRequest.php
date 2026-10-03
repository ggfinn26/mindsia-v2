<?php

namespace App\Http\Requests\Attendance;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeaveRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()->employee_id;
    }

    public function rules(): array
    {
        return [
            'leave_type' => ['required', 'in:permission,sick,leave'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['required', 'string'],
            'attachment' => ['nullable', 'file', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'leave_type.required' => 'Jenis permohonan wajib dipilih.',
            'leave_type.in' => 'Jenis permohonan tidak valid.',
            'start_date.required' => 'Tanggal mulai wajib diisi.',
            'start_date.date' => 'Tanggal mulai tidak valid.',
            'start_date.after_or_equal' => 'Tanggal mulai tidak boleh sebelum hari ini.',
            'end_date.required' => 'Tanggal selesai wajib diisi.',
            'end_date.date' => 'Tanggal selesai tidak valid.',
            'end_date.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
            'reason.required' => 'Alasan / keterangan wajib diisi.',
            'attachment.file' => 'Lampiran tidak valid.',
            'attachment.max' => 'Lampiran maksimal 5MB.',
        ];
    }
}
