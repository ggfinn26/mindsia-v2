<?php

namespace App\Http\Requests\Letter;

use Illuminate\Foundation\Http\FormRequest;

class StoreSopDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('letter.sop.create');
    }

    public function rules(): array
    {
        return [
            'branch_id' => ['nullable', 'exists:branches,id'],
            'category' => ['required', 'string', 'max:60'],
            'title' => ['required', 'string', 'max:150'],
            'document_code' => ['nullable', 'string', 'max:30', 'unique:sop_documents,document_code'],
            'version' => ['required', 'string', 'max:20'],
            'file' => ['required', 'file', 'max:20480', 'mimes:pdf,doc,docx'],
            'effective_date' => ['required', 'date'],
            'visible_to' => ['nullable', 'array'],
            'visible_to.*' => ['string'],
            'is_active' => ['boolean'],
        ];
    }
}
