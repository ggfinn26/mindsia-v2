<?php

namespace App\Http\Requests\Letter;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreOutLetterViaUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('letter.upload.create');
    }

    public function rules(): array
    {
        return [
            'branch_id' => ['required', 'exists:branches,id'],
            'letter_type' => ['nullable', 'string', 'max:50'],
            'letter_number' => ['nullable', 'string', 'max:100'],
            'letter_date' => ['nullable', 'date'],
            'recipient' => ['nullable', 'string', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255'],
            'file' => ['required', 'file', 'max:20480', 'mimes:pdf,doc,docx'],
            'is_historical' => ['boolean'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            if ($this->boolean('is_historical')) {
                return;
            }

            $date = $this->input('letter_date');
            if ($date && $date < now()->toDateString()) {
                $v->errors()->add('letter_date', 'Tanggal surat tidak boleh sebelum hari ini. Centang "Data History" untuk entry data lama.');
            }
        });
    }
}
