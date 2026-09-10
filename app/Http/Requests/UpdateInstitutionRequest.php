<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInstitutionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('organization.institution.update');
    }

    public function rules(): array
    {
        return [
            'regions_id' => ['required', 'exists:regions,id'],
            'jenjang_institution' => ['required', 'in:SD,SMP,SMA,PERGURUAN TINGGI'],
            'institution_name' => ['required', 'string', 'max:255'],
        ];
    }
}
