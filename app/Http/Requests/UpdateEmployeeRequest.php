<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('employee'));
    }

    public function rules(): array
    {
        $employee = $this->route('employee');

        return [
            'full_name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:L,P'],
            'birthdate' => ['nullable', 'date'],
            'email' => ['nullable', 'email', Rule::unique('employees', 'email')->ignore($employee->id)],
            'whatsapp_number' => ['nullable', 'string', 'min:8', 'max:15'],
            'is_active' => ['boolean'],
            'is_hq' => ['boolean'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'area_id' => ['nullable', 'exists:areas,id'],
            'region_id' => ['nullable', 'exists:regions,id'],
        ];
    }
}
