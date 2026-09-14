<?php

namespace App\Http\Requests\Toefl;

use Illuminate\Foundation\Http\FormRequest;

class UpdateToeflAnswerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'question_id' => ['required', 'integer', 'exists:toefl_questions,id'],
            'selected_option' => ['required', 'in:A,B,C,D'],
        ];
    }
}
