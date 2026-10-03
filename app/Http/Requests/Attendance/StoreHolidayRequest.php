<?php

namespace App\Http\Requests\Attendance;

use Illuminate\Foundation\Http\FormRequest;

class StoreHolidayRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('attendance.holiday.manage');
    }

    public function rules(): array
    {
        return [
            'holiday_name' => 'required|string|max:255',
            'holiday_start_date' => 'required|date',
            'holiday_end_date' => 'required|date|after_or_equal:holiday_start_date',
        ];
    }

    public function messages(): array
    {
        return [
            'holiday_name.required' => 'Nama hari libur wajib diisi.',
            'holiday_name.max' => 'Nama hari libur maksimal 255 karakter.',
            'holiday_start_date.required' => 'Tanggal mulai wajib diisi.',
            'holiday_start_date.date' => 'Tanggal mulai tidak valid.',
            'holiday_end_date.required' => 'Tanggal selesai wajib diisi.',
            'holiday_end_date.date' => 'Tanggal selesai tidak valid.',
            'holiday_end_date.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
        ];
    }
}
