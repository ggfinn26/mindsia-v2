<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProgramRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('BOARD');
    }

    public function rules(): array
    {
        $programId = $this->route('program')->id;

        return [
            'program_name' => ['required', 'string', 'max:255'],
            'program_code' => ['required', 'string', 'max:50', Rule::unique('programs', 'program_code')->ignore($programId)],
            'program_price' => ['required', 'numeric', 'min:0'],
            'program_description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];
    }
}
