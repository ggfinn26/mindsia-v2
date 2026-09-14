<?php

namespace App\Http\Requests\Toefl;

use Illuminate\Foundation\Http\FormRequest;

class UpdateToeflPassageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('toefl.passage.update');
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'body_text' => ['nullable', 'string'],
            'audio_media_id' => ['nullable', 'exists:toefl_media,id'],
            'display_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
