<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProgramRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('curriculum.program.update');
    }

    public function rules(): array
    {
        $programId = $this->route('program')->id;

        return [
            'program_name' => ['required', 'string', 'max:255'],
            'program_code' => ['required', 'string', 'max:50', Rule::unique('programs', 'program_code')->ignore($programId)],
            'program_price' => ['required', 'numeric', 'min:0'],
            'admin_fee' => ['nullable', 'numeric', 'min:0'],
            'admin_fee_mode' => ['nullable', 'in:per_registration,per_transaction'],
            'admin_fee_timing' => ['nullable', 'in:on_registration,on_first_payment'],
            'program_description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
            'receipt_template' => ['nullable', 'file', 'mimes:docx', 'max:20480'],
            'is_active' => ['boolean'],
        ];
    }
}
