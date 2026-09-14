<?php

namespace App\Http\Requests\Attendance;

use Illuminate\Foundation\Http\FormRequest;

class ReviewLeaveRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('attendance.leave.review');
    }

    public function rules(): array
    {
        return [
            'rejection_reason' => ['nullable', 'string'],
        ];
    }
}
