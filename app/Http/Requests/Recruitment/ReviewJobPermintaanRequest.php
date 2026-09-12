<?php

namespace App\Http\Requests\Recruitment;

use Illuminate\Foundation\Http\FormRequest;

class ReviewJobPermintaanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('hrd_review_permintaan');
    }

    public function rules(): array
    {
        return [
            'decision' => ['required', 'in:approved,rejected'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
