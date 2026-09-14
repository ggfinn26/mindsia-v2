<?php

namespace App\Http\Requests\Facility;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFacilityTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('facility.ticket.update');
    }

    public function rules(): array
    {
        return [
            'priority' => ['sometimes', 'in:low,medium,high'],
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
            'cost_amount' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
