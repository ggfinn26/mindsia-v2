<?php

namespace App\Http\Requests\Recruitment;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOfferingLetterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('recruitment.offering_letter.update');
    }

    public function rules(): array
    {
        return [
            'offered_salary' => ['sometimes', 'numeric', 'min:0'],
            'start_date' => ['nullable', 'date'],
            'meeting_type' => ['nullable', 'in:online,offline'],
            'meeting_at' => ['nullable', 'date'],
            'meeting_link' => ['nullable', 'url', 'max:500'],
            'meeting_location' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
