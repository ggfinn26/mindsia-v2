<?php

namespace App\Http\Requests\Toefl;

use Illuminate\Foundation\Http\FormRequest;

class StoreToeflTestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('toefl.test.create');
    }

    public function rules(): array
    {
        return [
            'test_name' => ['required', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:4'],
            'is_trial' => ['boolean'],
            'listening_time_limit' => ['nullable', 'integer', 'min:1'],
            'structure_time_limit' => ['nullable', 'integer', 'min:1'],
            'reading_time_limit' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
