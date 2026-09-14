<?php

namespace App\Http\Requests\Landing;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLandingLeaderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('landing.leader.update');
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:150'],
            'title' => ['sometimes', 'string', 'max:100'],
            'photo' => ['nullable', 'file', 'mimes:webp', 'max:5120'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
