<?php

namespace App\Http\Requests\Marketing;

use Illuminate\Foundation\Http\FormRequest;

class AssignEmployeeSocializationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->canAny(['marketing.socialization.assign', 'marketing.socialization.update']);
    }

    public function rules(): array
    {
        return [
            'employee_ids' => ['required', 'array', 'min:1'],
            'employee_ids.*' => ['exists:employees,id'],
        ];
    }
}
