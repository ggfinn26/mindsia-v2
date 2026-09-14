<?php

namespace App\Http\Requests\ClassRoom;

use App\Models\ClassRoom;
use Illuminate\Foundation\Http\FormRequest;

class StoreClassRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('class.manage');
    }

    public function rules(): array
    {
        return [
            'program_id' => 'required|exists:programs,id',
            'branch_id' => 'required|exists:branches,id',
            'class_name' => 'required|string|max:'.ClassRoom::MAX_CLASS_NAME_LENGTH,
            'tutor_id' => 'nullable|exists:employees,id',
            'day_of_week' => 'required|in:'.implode(',', ClassRoom::VALID_DAYS),
            'week_count' => 'required|integer|min:'.ClassRoom::MIN_WEEK_COUNT.'|max:'.ClassRoom::MAX_WEEK_COUNT,
            'start_date' => 'required|date|after_or_equal:today',
            'start_time_primary' => 'required|date_format:H:i',
            'end_time_primary' => 'required|date_format:H:i|after:start_time_primary',
            'start_time_secondary' => 'nullable|date_format:H:i',
            'end_time_secondary' => 'nullable|date_format:H:i|after:start_time_secondary',
        ];
    }
}
