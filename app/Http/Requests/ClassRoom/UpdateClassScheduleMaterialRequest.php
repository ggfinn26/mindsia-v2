<?php

namespace App\Http\Requests\ClassRoom;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClassScheduleMaterialRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('class.session.update');
    }

    public function rules(): array
    {
        return [
            'material_taught' => ['nullable', 'string', 'max:255'],
        ];
    }
}
