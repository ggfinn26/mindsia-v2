<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProgramRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('BOARD');
    }

    public function rules(): array
    {
        return [
            'program_name' => ['required', 'string', 'max:255'],
            'program_code' => ['required', 'string', 'max:50', 'unique:programs,program_code'],
            'program_price' => ['required', 'numeric', 'min:0'],
            'program_description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];
    }
}
