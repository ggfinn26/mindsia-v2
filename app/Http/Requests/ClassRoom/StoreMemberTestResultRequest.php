<?php

namespace App\Http\Requests\ClassRoom;

use Illuminate\Foundation\Http\FormRequest;

class StoreMemberTestResultRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('class.test.score');
    }

    public function rules(): array
    {
        return [
            'level' => ['required', 'string', 'max:50'],
            'final_score' => ['required', 'integer', 'min:0'],
            'scores' => ['nullable', 'array'],
            'scores.*' => ['required', 'integer', 'min:0'],
        ];
    }
}
