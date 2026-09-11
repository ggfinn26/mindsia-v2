<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeEducationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $employee = $this->route('employee');

        return $employee && (
            auth()->user()->id === $employee->user_id ||
            auth()->user()->can('view', $employee)
        );
    }

    public function rules(): array
    {
        return [
            'level' => ['required', 'in:SD,SMP,SMA,D3,S1,S2,S3'],
            'institution' => ['required', 'string', 'max:200'],
            'major' => ['nullable', 'string', 'max:100'],
            'graduation_year' => ['nullable', 'digits:4', 'numeric', 'min:1950', 'max:'.date('Y')],
        ];
    }
}
