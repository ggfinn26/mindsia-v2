<?php

namespace App\Http\Requests\Facility;

use App\Models\FacilityTicket;
use Illuminate\Foundation\Http\FormRequest;

class ResolveFacilityTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var FacilityTicket $ticket */
        $ticket = $this->route('ticket');

        return $ticket->status === 'approved' && $this->user()->can('facility.ticket.resolve');
    }

    public function rules(): array
    {
        return [
            'notes' => ['nullable', 'string'],
            'cost_amount' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
