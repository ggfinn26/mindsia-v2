<?php

namespace App\Http\Requests\Curriculum;

use Illuminate\Foundation\Http\FormRequest;

class StoreCurriculumItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('curriculum.item.create');
    }

    public function rules(): array
    {
        return [
            'item_name' => ['required', 'string', 'max:255'],
            'sequence_number' => ['required', 'integer', 'min:1'],
            'material_type' => ['required', 'in:file,external_link'],
            'material_value' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];
    }
}
