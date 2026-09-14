<?php

namespace App\Http\Requests\Toefl;

use Illuminate\Foundation\Http\FormRequest;

class UpdateToeflQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('toefl.question.update');
    }

    public function rules(): array
    {
        return [
            'passage_id' => ['nullable', 'exists:toefl_passages,id'],
            'display_order' => ['nullable', 'integer', 'min:0'],
            'question_text' => ['sometimes', 'string'],
            'image_media_id' => ['nullable', 'exists:toefl_media,id'],
            'option_a' => ['sometimes', 'string', 'max:500'],
            'option_b' => ['sometimes', 'string', 'max:500'],
            'option_c' => ['sometimes', 'string', 'max:500'],
            'option_d' => ['sometimes', 'string', 'max:500'],
            'correct_option' => ['sometimes', 'in:A,B,C,D'],
            'difficulty' => ['sometimes', 'in:easy,intermediate,advanced'],
            'points' => ['nullable', 'integer', 'min:1'],
            'explanation' => ['nullable', 'string'],
        ];
    }
}
