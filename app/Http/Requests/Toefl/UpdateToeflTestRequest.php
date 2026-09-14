<?php

namespace App\Http\Requests\Toefl;

use App\Models\ToeflTest;
use Illuminate\Foundation\Http\FormRequest;

class UpdateToeflTestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('toefl.test.update')
            && $this->route('toeflTest')->status === ToeflTest::STATUS_DRAFT;
    }

    public function rules(): array
    {
        return [
            'test_name' => ['sometimes', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:4'],
            'is_trial' => ['boolean'],
            'listening_time_limit' => ['nullable', 'integer', 'min:1'],
            'structure_time_limit' => ['nullable', 'integer', 'min:1'],
            'reading_time_limit' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
