<?php

namespace App\Http\Requests\ClassRoom;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMemberCertificateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('class.certificate.update');
    }

    public function rules(): array
    {
        return [
            'certificate_number' => ['nullable', 'string', 'max:50'],
            'certificate_available' => ['required', 'in:available,not_available'],
            'certificate_hardcopy' => ['required', 'boolean'],
            'certificate_taken' => ['required', 'in:taken,not_taken'],
        ];
    }
}
