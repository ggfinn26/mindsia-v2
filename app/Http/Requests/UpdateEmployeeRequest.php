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
            'birthdate' => ['required', 'date'],
            'email' => ['required', 'email', Rule::unique('employees', 'email')->ignore($employee->id)],
            'whatsapp_number' => ['required', 'string'],
            'branch_id' => ['required', 'exists:branches,id'],
        ];
    }
}
