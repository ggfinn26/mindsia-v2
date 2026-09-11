<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OffBoardingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('employee'));
    }

    public function rules(): array
    {
        return [
            'off_boarding_date' => ['required', 'date'],
            'reason_off_boarding' => ['nullable', 'string'],
        ];
    }
}
