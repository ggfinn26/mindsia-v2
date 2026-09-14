<?php

namespace App\Http\Requests\Toefl;

use Illuminate\Foundation\Http\FormRequest;

class StoreToeflPassageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('toefl.passage.create');
    }

    public function rules(): array
    {
        return [
            'section' => ['required', 'in:listening,structure,reading'],
            'title' => ['required', 'string', 'max:255'],
            'body_text' => ['nullable', 'string'],
            'audio_media_id' => ['nullable', 'exists:toefl_media,id'],
            'display_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
