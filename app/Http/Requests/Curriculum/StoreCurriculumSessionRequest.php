<?php

namespace App\Http\Requests\Curriculum;

use Illuminate\Foundation\Http\FormRequest;

class StoreCurriculumSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('curriculum.session.create');
    }

    public function rules(): array
    {
        return [
            'session_number' => ['required', 'integer', 'min:1'],
            'session_title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'items' => ['nullable', 'array'],
            'items.*.item_name' => ['required', 'string', 'max:255'],
            'items.*.sequence_number' => ['required', 'integer', 'min:1'],
            'items.*.material_type' => ['required', 'in:file,external_link'],
            'items.*.material_value' => ['required', 'string'],
            'items.*.notes' => ['nullable', 'string'],
            'items.*.is_active' => ['boolean'],
        ];
    }
}
