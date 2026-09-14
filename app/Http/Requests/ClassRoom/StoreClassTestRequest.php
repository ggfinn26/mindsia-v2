<?php

namespace App\Http\Requests\ClassRoom;

use Illuminate\Foundation\Http\FormRequest;

class StoreClassTestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('class.test.create');
    }

    public function rules(): array
    {
        return [
            'test_name' => ['required', 'string', 'max:255'],
            'test_type' => ['required', 'in:pre_test,post_test'],
            'date' => ['required', 'date'],
            'description' => ['nullable', 'string'],
            'criteria' => ['nullable', 'array'],
            'criteria.*.criteria_name' => ['required', 'string', 'max:255'],
            'criteria.*.description' => ['nullable', 'string'],
        ];
    }
}
