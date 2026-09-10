<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignBranchPICRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('organization.branch.assign_pic');
    }

    public function rules(): array
    {
        return [
            'ma_pic_employee_id' => ['required', 'exists:employees,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'ma_pic_employee_id.required' => 'Employee PIC harus dipilih.',
            'ma_pic_employee_id.exists' => 'Employee PIC tidak ditemukan.',
        ];
    }
}
