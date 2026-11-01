<?php

namespace App\Http\Requests\Attendance;

use App\Services\Attendance\WorkScheduleOverlapChecker;
use Illuminate\Foundation\Http\FormRequest;

class StoreWorkScheduleRuleRequest extends FormRequest
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
            'setting_name' => 'required|string|max:100|unique:work_schedule_rules',
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
            'assign_assignable_type' => 'nullable|in:employee,position,role',
            'assign_assignable_id' => 'nullable|array',
            'assign_assignable_id.*' => 'integer|min:1',
            'assign_effective_start_date' => 'required_with:assign_assignable_type|nullable|date',
            'assign_effective_end_date' => 'nullable|date|after_or_equal:assign_effective_start_date',
            'assign_is_active' => 'boolean',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $type = $this->input('assign_assignable_type');
            $ids = array_map('intval', array_filter((array) $this->input('assign_assignable_id')));

            if (! $type || ! $ids || $validator->errors()->isNotEmpty() || ! $this->boolean('assign_is_active', true)) {
                return;
            }

            $model = WorkScheduleOverlapChecker::TYPE_MAP[$type];
            $missing = array_diff($ids, $model::whereIn('id', $ids)->pluck('id')->all());
            if ($missing) {
                $validator->errors()->add('assign_assignable_id', "Target {$type} tidak ditemukan (id: ".implode(', ', $missing).').');

                return;
            }

            $message = app(WorkScheduleOverlapChecker::class)->firstConflict(
                $type, $ids, (string) $this->input('setting_name'), array_map('intval', (array) $this->input('work_days')),
                $this->input('assign_effective_start_date'), $this->input('assign_effective_end_date'),
            );

            if ($message) {
                $validator->errors()->add('assign_assignable_id', $message);
            }
        });
    }
}
