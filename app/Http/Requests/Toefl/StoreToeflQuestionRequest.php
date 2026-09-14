<?php

namespace App\Http\Requests\Toefl;

use Illuminate\Foundation\Http\FormRequest;

class StoreToeflQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('toefl.question.create');
    }

    public function rules(): array
    {
        return [
            'passage_id' => ['nullable', 'exists:toefl_passages,id'],
            'section' => ['required', 'in:listening,structure,reading'],
            'display_order' => ['nullable', 'integer', 'min:0'],
            'question_text' => ['required', 'string'],
            'image_media_id' => ['nullable', 'exists:toefl_media,id'],
            'option_a' => ['required', 'string', 'max:500'],
            'option_b' => ['required', 'string', 'max:500'],
            'option_c' => ['required', 'string', 'max:500'],
            'option_d' => ['required', 'string', 'max:500'],
            'correct_option' => ['required', 'in:A,B,C,D'],
            'difficulty' => ['required', 'in:easy,intermediate,advanced'],
            'points' => ['nullable', 'integer', 'min:1'],
            'explanation' => ['nullable', 'string'],
        ];
    }
}
