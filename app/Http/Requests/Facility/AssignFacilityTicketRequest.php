<?php

namespace App\Http\Requests\Facility;

use Illuminate\Foundation\Http\FormRequest;

class AssignFacilityTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('facility.ticket.assign');
    }

    public function rules(): array
    {
        return [
            'assigned_to_employee_id' => ['nullable', 'exists:employees,id'],
        ];
    }
}
