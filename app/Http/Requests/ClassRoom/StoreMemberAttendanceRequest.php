<?php

namespace App\Http\Requests\ClassRoom;

use Illuminate\Foundation\Http\FormRequest;

class StoreMemberAttendanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        if (! $user->can('class.attendance.record')) {
            return false;
        }
        if ($user->can('class.manage')) {
            return true;
        }
        $schedule = $this->route('schedule');

        return $schedule->classRoom?->tutor_id === $user->employee?->id;
    }

    public function rules(): array
    {
        return [
            'attendances' => ['required', 'array'],
            'attendances.*.member_class_id' => ['required', 'integer', 'exists:member_class,id'],
            'attendances.*.status' => ['required', 'in:present,absent,late,excused,sick'],
            'attendances.*.notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
