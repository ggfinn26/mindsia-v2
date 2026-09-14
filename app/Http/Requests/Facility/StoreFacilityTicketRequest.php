<?php

namespace App\Http\Requests\Facility;

use Illuminate\Foundation\Http\FormRequest;

class StoreFacilityTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('facility.ticket.create');
    }

    public function rules(): array
    {
        return [
            'branch_id' => ['required', 'exists:branches,id'],
            'category' => ['required', 'string', 'max:100'],
            'priority' => ['required', 'in:low,medium,high'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
        ];
    }
}
