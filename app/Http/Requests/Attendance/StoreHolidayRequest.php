<?php

namespace App\Http\Requests\Attendance;

use Illuminate\Foundation\Http\FormRequest;

class StoreHolidayRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('attendance.holiday.manage');
    }

    public function rules(): array
    {
        return [
            'holiday_name' => 'required|string|max:255',
            'holiday_start_date' => 'required|date',
            'holiday_end_date' => 'required|date|after_or_equal:holiday_start_date',
        ];
    }
}
