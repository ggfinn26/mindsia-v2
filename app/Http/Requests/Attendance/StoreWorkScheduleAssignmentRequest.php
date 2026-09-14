<?php

namespace App\Http\Requests\Attendance;

use Illuminate\Foundation\Http\FormRequest;

class StoreWorkScheduleAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('attendance.holiday.manage');
    }

    public function rules(): array
    {
        return [
            'work_schedule_rule_id' => 'required|exists:work_schedule_rules,id',
            'assignable_type' => 'required|in:employee,position,role',
            'assignable_id' => 'required|integer|min:1',
            'effective_start_date' => 'required|date',
            'effective_end_date' => 'nullable|date|after_or_equal:effective_start_date',
            'is_active' => 'boolean',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $type = $this->input('assignable_type');
            $id = $this->input('assignable_id');

            $tableMap = [
                'employee' => 'employees',
                'position' => 'positions',
                'role' => 'roles',
            ];

            $table = $tableMap[$type] ?? null;
            if ($table && ! \DB::table($table)->where('id', $id)->exists()) {
                $validator->errors()->add('assignable_id', "The selected $type does not exist.");
            }
        });
    }
}
