<?php

namespace App\Http\Requests\ClassRoom;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClassRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('board-of-directors');
    }

    public function rules(): array
    {
        return [
            'class_name' => 'required|string|max:255',
            'tutor_id' => 'nullable|exists:employees,id',
        ];
    }
}
