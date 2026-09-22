<?php

namespace App\Http\Requests\Curriculum;

use Illuminate\Foundation\Http\FormRequest;

class SubmitInteractiveAnswerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('member')->check();
    }

    public function rules(): array
    {
        return [
            'answers' => ['required', 'array'],
            'answers.*' => ['nullable', 'in:A,B,C,D'],
        ];
    }
}
