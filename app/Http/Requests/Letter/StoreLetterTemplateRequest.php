<?php

namespace App\Http\Requests\Letter;

use Illuminate\Foundation\Http\FormRequest;

class StoreLetterTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('letter.template.create');
    }

    public function rules(): array
    {
        return [
            'template_code' => ['required', 'string', 'max:50', 'unique:letter_templates,template_code'],
            'template_name' => ['required', 'string', 'max:100'],
            'letter_category' => ['required', 'in:generated,marketing,announcement'],
            'letter_number_format' => ['nullable', 'string', 'max:100'],
            'file' => ['nullable', 'file', 'max:20480', 'mimes:docx'],
            'is_active' => ['boolean'],
        ];
    }
}
