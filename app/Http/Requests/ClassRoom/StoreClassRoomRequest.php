<?php

namespace App\Http\Requests\ClassRoom;

use Illuminate\Foundation\Http\FormRequest;

class StoreClassRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('board-of-directors');
    }

    public function rules(): array
    {
        return [
            'program_id' => 'required|exists:programs,id',
            'branch_id' => 'required|exists:branches,id',
            'class_name' => 'required|string|max:255',
            'tutor_id' => 'nullable|exists:employees,id',
            'day_of_week' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'week_count' => 'required|integer|min:1|max:52',
            'start_date' => 'required|date|after_or_equal:today',
            'start_time_primary' => 'required|date_format:H:i',
            'end_time_primary' => 'required|date_format:H:i|after:start_time_primary',
            'start_time_secondary' => 'nullable|date_format:H:i',
            'end_time_secondary' => 'nullable|date_format:H:i',
        ];
    }
}
