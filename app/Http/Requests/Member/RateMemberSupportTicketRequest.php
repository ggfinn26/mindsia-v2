<?php

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;

class RateMemberSupportTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'rating_notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
