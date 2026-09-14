<?php

namespace App\Http\Requests\Letter;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSopDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('letter.sop.update');
    }

    public function rules(): array
    {
        $id = $this->route('sopDocument')->id;

        return [
            'category' => ['sometimes', 'string', 'max:60'],
            'title' => ['sometimes', 'string', 'max:150'],
            'document_code' => ['nullable', 'string', 'max:30', "unique:sop_documents,document_code,{$id}"],
            'version' => ['sometimes', 'string', 'max:20'],
            'effective_date' => ['sometimes', 'date'],
            'visible_to' => ['nullable', 'array'],
            'visible_to.*' => ['string'],
            'is_active' => ['boolean'],
        ];
    }
}
