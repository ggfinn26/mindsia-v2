<?php

namespace App\Http\Requests\Landing;

use Illuminate\Foundation\Http\FormRequest;

class UpdateActivityPhotoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('landing.photo.update');
    }

    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:150'],
            'caption' => ['nullable', 'string'],
            'photo' => ['sometimes', 'file', 'mimes:webp', 'max:5120'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
