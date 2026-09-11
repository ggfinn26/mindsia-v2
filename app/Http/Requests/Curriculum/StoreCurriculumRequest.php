<?php

namespace App\Http\Requests\Curriculum;

use Illuminate\Foundation\Http\FormRequest;

class StoreCurriculumRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('BOARD');
    }

    public function rules(): array
    {
        return [
            'program_id' => ['required', 'exists:programs,id'],
            'curriculum_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
            'sessions' => ['nullable', 'array'],
            'sessions.*.session_number' => ['required', 'integer', 'min:1'],
            'sessions.*.session_title' => ['required', 'string', 'max:150'],
            'sessions.*.description' => ['nullable', 'string'],
            'sessions.*.sort_order' => ['nullable', 'integer', 'min:0'],
            'sessions.*.is_active' => ['boolean'],
            'sessions.*.items' => ['nullable', 'array'],
            'sessions.*.items.*.item_name' => ['required', 'string', 'max:255'],
            'sessions.*.items.*.sequence_number' => ['required', 'integer', 'min:1'],
            'sessions.*.items.*.material_type' => ['required', 'in:file,external_link'],
            'sessions.*.items.*.material_value' => ['required', 'string'],
            'sessions.*.items.*.notes' => ['nullable', 'string'],
            'sessions.*.items.*.is_active' => ['boolean'],
        ];
    }
}
