<?php

namespace App\Http\Requests\Letter;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSopDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('letter.sop.update');
    }

    public function rules(): array
    {
        return [
            'category' => ['sometimes', 'string', 'max:60'],
            'title' => ['sometimes', 'string', 'max:150'],
            'document_code' => ['nullable', 'string', 'max:30', Rule::unique('sop_documents', 'document_code')->ignore($this->route('sop_document'))],
            'version' => ['sometimes', 'string', 'max:20'],
            'effective_date' => ['sometimes', 'date'],
            'visible_to' => ['nullable', 'array'],
            'visible_to.*' => ['string'],
            'is_active' => ['boolean'],
        ];
    }
}
