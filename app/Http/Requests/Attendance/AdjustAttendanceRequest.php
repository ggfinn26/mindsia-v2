<?php

namespace App\Http\Requests\Attendance;

use App\Models\EmployeeWorkAttendanceLog;
use Illuminate\Foundation\Http\FormRequest;

class AdjustAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('attendance.adjustment.create');
    }

    public function rules(): array
    {
        return [
            'status' => ['nullable', 'in:absent,checked_in,present,sick,permission,leave,holiday'],
            'check_in' => ['nullable', 'date_format:Y-m-d H:i:s'],
            'check_out' => ['nullable', 'date_format:Y-m-d H:i:s', 'after_or_equal:check_in'],
            'late_minutes' => ['nullable', 'integer', 'min:0'],
            'adjustment_reason' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'adjustment_reason.required' => 'Catatan koreksi wajib diisi.',
            'check_in.date_format' => 'Format jam masuk tidak valid.',
            'check_out.date_format' => 'Format jam keluar tidak valid.',
            'check_out.after_or_equal' => 'Jam keluar harus sama atau setelah jam masuk.',
            'late_minutes.integer' => 'Menit terlambat harus berupa angka.',
            'late_minutes.min' => 'Menit terlambat tidak boleh negatif.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // View sends H:i from <input type="time">; prefix with log's attendance_date
        $log = $this->route('attendanceLog');
        $date = $log instanceof EmployeeWorkAttendanceLog
            ? $log->attendance_date->toDateString()
            : now()->toDateString();

        $merged = [];
        foreach (['check_in', 'check_out'] as $field) {
            $val = $this->input($field);
            if ($val && preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $val)) {
                $merged[$field] = $date.' '.substr($val, 0, 5).':00';
            }
        }

        if ($merged) {
            $this->merge($merged);
        }
    }
}
