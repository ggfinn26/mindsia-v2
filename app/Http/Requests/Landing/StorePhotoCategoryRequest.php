<?php

namespace App\Http\Requests\Landing;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePhotoCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('landing.photo.manage');
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100', 'unique:photo_categories,name'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
