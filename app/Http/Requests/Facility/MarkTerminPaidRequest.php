<?php

namespace App\Http\Requests\Facility;

use App\Models\BranchRentTermin;
use Illuminate\Foundation\Http\FormRequest;

class MarkTerminPaidRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var BranchRentTermin $termin */
        $termin = $this->route('termin');

        return in_array($termin->status, ['unpaid', 'overdue'])
            && $this->user()->can('facility.rent_contract.mark_paid');
    }

    public function rules(): array
    {
        return [
            'notes' => ['nullable', 'string'],
        ];
    }
}
