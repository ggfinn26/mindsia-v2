<?php

namespace App\Http\Requests\Recruitment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreOfferingLetterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('recruitment.offering_letter.create');
    }

    public function rules(): array
    {
        return [
            'offered_salary' => ['required', 'numeric', 'min:0'],
            'start_date' => ['nullable', 'date'],
            'meeting_type' => ['nullable', 'in:online,offline'],
            'meeting_at' => ['nullable', 'date', 'after:now'],
            'meeting_link' => ['nullable', 'url', 'max:500'],
            'meeting_location' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            $type = $this->input('meeting_type');
            if ($type === 'online' && ! $this->filled('meeting_link')) {
                $v->errors()->add('meeting_link', 'meeting_link wajib untuk pertemuan online.');
            }
            if ($type === 'offline' && ! $this->filled('meeting_location')) {
                $v->errors()->add('meeting_location', 'meeting_location wajib untuk pertemuan offline.');
            }
        });
    }
}
