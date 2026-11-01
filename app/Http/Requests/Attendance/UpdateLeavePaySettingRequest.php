<?php

namespace App\Http\Requests\Attendance;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLeavePaySettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('leave.pay_settings.manage');
    }

    public function rules(): array
    {
        return [
            'position_id' => ['required', 'exists:positions,id'],
            'leave_type' => ['required', 'in:permission,sick,leave,absent,absent_session'],
            'is_paid' => ['required', 'boolean'],
        ];
    }
}
