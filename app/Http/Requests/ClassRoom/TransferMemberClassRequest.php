<?php

namespace App\Http\Requests\ClassRoom;

use Illuminate\Foundation\Http\FormRequest;

class TransferMemberClassRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('class.manage');
    }

    public function rules(): array
    {
        return [
            'new_class_id' => 'required|exists:classes,id',
        ];
    }
}
