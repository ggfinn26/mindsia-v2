<?php

namespace App\Http\Requests\Attendance;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWorkScheduleRuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('attendance.schedule_rule.manage');
    }

    protected function prepareForValidation(): void
    {
        $fields = ['start_time', 'end_time', 'break_start_time', 'break_end_time'];
        $merged = [];
        foreach ($fields as $field) {
            $val = $this->input($field);
            if ($val && strlen($val) > 5) {
                $merged[$field] = substr($val, 0, 5);
            }
        }
        if ($merged) {
            $this->merge($merged);
        }
    }

    public function rules(): array
    {
        return [
            'setting_name' => 'required|string|max:100|unique:work_schedule_rules,setting_name,'.$this->route('work_schedule_rule')->id,
            'start_time' => 'required_if:is_required,1|nullable|date_format:H:i',
            'end_time' => 'required_if:is_required,1|nullable|date_format:H:i|after:start_time',
            'break_start_time' => 'nullable|date_format:H:i',
            'break_end_time' => 'nullable|date_format:H:i|after:break_start_time',
            'late_tolerance_minutes' => 'required|integer|min:0|max:120',
            'early_leave_tolerance_minutes' => 'required|integer|min:0|max:120',
            'is_required' => 'boolean',
            'is_active' => 'boolean',
            'work_days' => 'nullable|array',
            'work_days.*' => 'integer|between:1,7',
        ];
    }
}
