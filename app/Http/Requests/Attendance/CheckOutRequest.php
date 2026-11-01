<?php

namespace App\Http\Requests\Attendance;

use Illuminate\Foundation\Http\FormRequest;

class CheckOutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()->employee_id;
    }

    public function rules(): array
    {
        return [
            'check_out_latitude' => ['nullable', 'required_with:check_out_longitude', 'numeric', 'between:-90,90'],
            'check_out_longitude' => ['nullable', 'required_with:check_out_latitude', 'numeric', 'between:-180,180'],
            'selfie' => ['nullable', 'image', 'max:5120'],
            'check_out_notes' => ['required', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return ['check_out_notes.required' => 'Isi dulu apa yang sudah dikerjakan hari ini.'];
    }
}
