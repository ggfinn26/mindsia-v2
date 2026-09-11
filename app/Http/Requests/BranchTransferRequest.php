<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BranchTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $employee = $this->route('employee');

        return [
            'to_branch_id' => [
                'required',
                'exists:branches,id',
                "different:{$employee->branch_id}",
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
