<?php

namespace App\Http\Requests\Attendance;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeaveRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()->employee_id;
    }

    public function rules(): array
    {
        return [
            'leave_type' => ['required', 'in:permission,sick,leave'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['required', 'string'],
            'attachment' => ['nullable', 'file', 'max:5120'],
        ];
    }
}
