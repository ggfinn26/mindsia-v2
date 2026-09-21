<?php

namespace App\Http\Requests\ClassRoom;

use App\Models\Branch;
use App\Models\ClassRoom;
use App\Models\Employee;
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
            'tutor_id' => ['required', 'exists:employees,id', function ($attr, $value, $fail) {
                $branch = Branch::find($this->input('branch_id'));
                if (! $branch) {
                    return;
                }
                $tutor = Employee::find($value);
                if (! $tutor) {
                    return;
                }
                $sameArea = $tutor->area_id && $tutor->area_id === $branch->areas_id;
                $sameBranch = $tutor->branch_id === $branch->id;
                if (! $sameBranch && ! $sameArea) {
                    $fail('Tutor tidak berasal dari cabang atau area yang sama dengan kelas.');
                }
            }],
            'day_of_week' => 'required|in:'.implode(',', ClassRoom::PRIMARY_DAYS),
            'week_count' => 'required|integer|min:'.ClassRoom::MIN_WEEK_COUNT.'|max:'.ClassRoom::MAX_WEEK_COUNT,
            'start_date' => 'required|date|after_or_equal:today',
            'start_time_primary' => 'required|date_format:H:i',
            'end_time_primary' => 'required|date_format:H:i|after:start_time_primary',
            'start_time_secondary' => 'nullable|date_format:H:i',
            'end_time_secondary' => 'nullable|date_format:H:i|after:start_time_secondary',
        ];
    }
}
