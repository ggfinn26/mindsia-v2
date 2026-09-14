<?php

namespace App\Http\Requests\Attendance;

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
            'status' => ['nullable', 'in:absent,checked_in,present,present_late,late,sick,permission,leave,holiday'],
            'check_in' => ['nullable', 'date_format:Y-m-d H:i:s'],
            'check_out' => ['nullable', 'date_format:Y-m-d H:i:s', 'after_or_equal:check_in'],
            'late_minutes' => ['nullable', 'integer', 'min:0'],
            'adjustment_reason' => ['required', 'string'],
        ];
    }
}
