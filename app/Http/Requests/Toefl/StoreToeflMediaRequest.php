<?php

namespace App\Http\Requests\Toefl;

use Illuminate\Foundation\Http\FormRequest;

class StoreToeflMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('toefl.media.create');
    }

    public function rules(): array
    {
        return [
            'media_type' => ['required', 'in:audio,image'],
            'file' => ['required', 'file', 'max:20480'],
        ];
    }
}
