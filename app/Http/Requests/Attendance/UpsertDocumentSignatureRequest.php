<?php

namespace App\Http\Requests\Attendance;

use App\Models\DocumentSignatureSetting;
use Illuminate\Foundation\Http\FormRequest;

class UpsertDocumentSignatureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('attendance.document_signature.manage');
    }

    public function rules(): array
    {
        $types = implode(',', DocumentSignatureSetting::$documentTypes);

        return [
            'signers' => ['required', 'array'],
            'signers.*.signer_name' => ['required', 'string', 'max:255'],
            'signers.*.signer_title' => ['required', 'string', 'max:255'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $allowed = DocumentSignatureSetting::$documentTypes;
            foreach (array_keys($this->input('signers', [])) as $type) {
                if (! in_array($type, $allowed)) {
                    $validator->errors()->add('signers', "Tipe dokumen tidak valid: {$type}");
                }
            }
        });
    }
}
