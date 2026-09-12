<?php

namespace App\Http\Requests\Recruitment;

use Illuminate\Foundation\Http\FormRequest;

class NegotiateOfferingLetterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage offering letters');
    }

    public function rules(): array
    {
        return [
            'agreed_salary' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
