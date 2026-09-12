<?php

namespace App\Http\Requests\Attendance;

use Illuminate\Foundation\Http\FormRequest;

class ReviewLeaveRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(['BOARD', 'HRR', 'HRP']);
    }

    public function rules(): array
    {
        return [
            'rejection_reason' => ['required_if:_action,reject', 'nullable', 'string'],
        ];
    }
}
