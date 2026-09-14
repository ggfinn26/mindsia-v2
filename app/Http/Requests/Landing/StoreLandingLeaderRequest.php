<?php

namespace App\Http\Requests\Landing;

use Illuminate\Foundation\Http\FormRequest;

class StoreLandingLeaderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('landing.leader.create');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'title' => ['required', 'string', 'max:100'],
            'photo' => ['nullable', 'file', 'mimes:webp', 'max:5120'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
