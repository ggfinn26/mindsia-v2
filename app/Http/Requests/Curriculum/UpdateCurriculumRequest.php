<?php

namespace App\Http\Requests\Curriculum;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCurriculumRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('curriculum.curriculum.update');
    }

    public function rules(): array
    {
        return [
            'curriculum_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];
    }
}
