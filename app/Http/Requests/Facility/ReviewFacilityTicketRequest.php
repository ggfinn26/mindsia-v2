<?php

namespace App\Http\Requests\Facility;

use App\Models\FacilityTicket;
use Illuminate\Foundation\Http\FormRequest;

class ReviewFacilityTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var FacilityTicket $ticket */
        $ticket = $this->route('facilityTicket');

        return $ticket->status === 'pending_review' && $this->user()->can('facility.ticket.review');
    }

    public function rules(): array
    {
        return [
            'action' => ['required', 'in:approve,reject'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
