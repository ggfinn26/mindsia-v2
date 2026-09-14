<?php

namespace App\Http\Requests\Landing;

use Illuminate\Foundation\Http\FormRequest;

class StoreActivityPhotoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('landing.photo.create');
    }

    public function rules(): array
    {
        return [
            'section' => ['required', 'in:home,company'],
            'title' => ['nullable', 'string', 'max:150'],
            'caption' => ['nullable', 'string'],
            'photo' => ['required', 'file', 'mimes:webp', 'max:5120'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
