<?php

namespace App\Http\Requests;

use App\Models\Employee;
use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Employee::class);
    }

    public function rules(): array
    {
        return [
            'employee_code' => ['required', 'string', 'unique:employees'],
            'full_name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:L,P'],
            'birthdate' => ['required', 'date'],
            'email' => ['required', 'email', 'unique:employees'],
            'whatsapp_number' => ['required', 'string'],
            'branch_id' => ['required', 'exists:branches,id'],
            'region_id' => ['required', 'exists:regions,id'],
            'area_id' => ['nullable', 'exists:areas,id'],
        ];
    }
}
