<?php

namespace App\Http\Requests\Marketing;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeWaTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('marketing.wa_template.manage');
    }

    public function rules(): array
    {
        return [
            'template_name' => ['required', 'string', 'max:100'],
            'template_body' => ['required', 'string'],
        ];
    }
}
