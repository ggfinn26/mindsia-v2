<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBranchProgramQuotaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('curriculum.quota.update');
    }

    public function rules(): array
    {
        return [
            'quota_limit' => ['required', 'integer', 'min:0'],
        ];
    }
}
