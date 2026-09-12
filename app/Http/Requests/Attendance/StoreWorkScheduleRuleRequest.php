<?php

namespace App\Http\Requests\Attendance;

use Illuminate\Foundation\Http\FormRequest;

class StoreWorkScheduleRuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('board-of-directors');
    }

    public function rules(): array
    {
        return [
            'setting_name' => 'required|string|max:100|unique:work_schedule_rules',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'break_start_time' => 'nullable|date_format:H:i',
            'break_end_time' => 'nullable|date_format:H:i|after:break_start_time',
            'late_tolerance_minutes' => 'required|integer|min:0|max:120',
            'early_leave_tolerance_minutes' => 'required|integer|min:0|max:120',
            'is_required' => 'boolean',
            'is_active' => 'boolean',
        ];
    }
}
