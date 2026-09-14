<?php

namespace App\Http\Requests\Letter;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOutLetterViaUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('letter.upload.update');
    }

    public function rules(): array
    {
        return [
            'letter_type' => ['nullable', 'string', 'max:50'],
            'letter_number' => ['nullable', 'string', 'max:100'],
            'letter_date' => ['nullable', 'date'],
            'recipient' => ['nullable', 'string', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
