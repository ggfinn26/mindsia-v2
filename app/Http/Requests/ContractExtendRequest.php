<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContractExtendRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('contract.manage');
    }

    public function rules(): array
    {
        return [
            'proposed_end_date' => ['required', 'date', 'after:today'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}
