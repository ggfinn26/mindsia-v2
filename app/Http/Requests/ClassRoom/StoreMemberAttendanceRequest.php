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
        return $this->user()->can('class.attendance.record');
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
