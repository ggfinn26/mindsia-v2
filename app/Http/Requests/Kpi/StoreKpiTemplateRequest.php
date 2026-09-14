<?php

namespace App\Http\Requests\Kpi;

use Illuminate\Foundation\Http\FormRequest;

class StoreKpiTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('kpi.template.create');
    }

    public function rules(): array
    {
        return [
            'template_code' => ['required', 'string', 'max:50', 'unique:kpi_templates,template_code'],
            'template_name' => ['required', 'string', 'max:255'],
            'position_id' => ['nullable', 'exists:positions,id'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];
    }
}
