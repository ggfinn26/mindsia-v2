<?php

namespace App\Http\Requests\ClassRoom;

use Illuminate\Foundation\Http\FormRequest;

class StoreMemberClassRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('class.manage');
    }

    public function rules(): array
    {
        return [
            'member_registration_id' => 'required|integer|exists:members_registration,id',
            'start_date' => 'required|date|after_or_equal:today',
        ];
    }
}
