<?php

namespace App\Http\Requests\Facility;

use Illuminate\Foundation\Http\FormRequest;

class StoreFacilityTicketAttachmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('facility.ticket.update');
    }

    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png'],
        ];
    }
}
