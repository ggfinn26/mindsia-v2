<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBranchProgramQuotaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('curriculum.quota.create');
    }

    public function rules(): array
    {
        return [
            'branch_id' => ['required', 'integer', 'exists:branches,id'],
            'quota_limit' => ['required', 'integer', 'min:0'],
        ];
    }
}
