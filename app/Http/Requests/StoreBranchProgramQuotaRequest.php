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
            'quotas' => ['nullable', 'array'],
            'quotas.*' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
