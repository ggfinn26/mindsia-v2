<?php

namespace App\Http\Requests\ClassRoom;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMemberAttendanceRequest extends FormRequest
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
            'status' => ['required', 'in:present,absent,late,excused,sick'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
