<?php

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMemberRegistrationGraduationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(['BOARD_OF_DIRECTORS', 'MARKETING']);
    }

    public function rules(): array
    {
        return [
            'graduation_status' => ['required', 'in:BELUM_LULUS,LULUS'],
        ];
    }
}
