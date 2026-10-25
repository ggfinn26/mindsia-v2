<?php

namespace App\Http\Requests\Landing;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePhotoCategoryRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:100', Rule::unique('photo_categories', 'name')->ignore($this->route('category'))],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
