<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContractChangePositionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'position_id' => ['required', 'exists:positions,id', 'different:current_position_id'],
            'effective_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}
