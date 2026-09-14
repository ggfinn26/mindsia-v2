<?php

namespace App\Http\Requests\Kpi;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateKpiTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('kpi.template.update');
    }

    public function rules(): array
    {
        return [
            'template_code' => ['required', 'string', 'max:50', Rule::unique('kpi_templates', 'template_code')->ignore($this->route('kpiTemplate'))],
            'template_name' => ['required', 'string', 'max:255'],
            'position_id' => ['nullable', 'exists:positions,id'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];
    }
}
