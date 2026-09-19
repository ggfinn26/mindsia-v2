<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BranchTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        $employee = $this->route('employee');

        return $this->user()->can('requestBranchTransfer', $employee)
            || $this->user()->can('employee.update');
    }

    public function rules(): array
    {
        $employee = $this->route('employee');

        return [
            'to_branch_id' => [
                'required',
                'exists:branches,id',
                "not_in:{$employee->branch_id}",
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'to_branch_id.required' => 'Cabang tujuan harus dipilih.',
            'to_branch_id.exists' => 'Cabang tujuan tidak valid.',
            'to_branch_id.different' => 'Cabang tujuan harus berbeda dari cabang saat ini.',
        ];
    }
}
