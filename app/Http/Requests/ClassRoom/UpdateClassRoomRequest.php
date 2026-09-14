<?php

namespace App\Http\Requests\ClassRoom;

use App\Models\ClassRoom;
use Illuminate\Foundation\Http\FormRequest;

class UpdateClassRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('class.manage');
    }

    public function rules(): array
    {
        return [
            'class_name' => 'required|string|max:'.ClassRoom::MAX_CLASS_NAME_LENGTH,
            'tutor_id' => 'nullable|exists:employees,id',
        ];
    }
}
