<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmploymentStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('contract.manage');
    }

    public function rules(): array
    {
        $tab = $this->input('tab', 'lengkap');
        $common = [
            'tab' => ['required', 'in:lengkap,cepat'],
            'type_employment' => ['required', 'string', 'max:50'],
            'join_date' => ['required', 'date'],
            'position_id' => ['required', 'exists:positions,id'],
        ];

        if ($tab === 'cepat') {
            return array_merge($common, [
                'contract_file_path' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
            ]);
        }

        return array_merge($common, [
            'contract_start_date' => ['required', 'date'],
            'contract_end_date' => ['nullable', 'date', 'after_or_equal:contract_start_date'],
        ]);
    }
}
