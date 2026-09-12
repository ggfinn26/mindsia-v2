<?php

namespace App\Http\Requests\Attendance;

use Illuminate\Foundation\Http\FormRequest;

class SessionCheckOutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()->employee_id;
    }

    public function rules(): array
    {
        return [
            'check_out_latitude' => ['required', 'numeric', 'between:-90,90'],
            'check_out_longitude' => ['required', 'numeric', 'between:-180,180'],
            'check_out_distance_m' => ['required', 'integer', 'min:0'],
            'selfie' => ['nullable', 'image', 'max:5120'],
            'check_out_notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}
