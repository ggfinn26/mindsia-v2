<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRegionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('organization.region.update');
    }

    public function rules(): array
    {
        return [
            'province_id' => ['required', 'exists:provinces,id'],
            'name' => ['required', 'string', 'max:100', 'unique:regions,name,'.$this->route('region')->id],
        ];
    }
}
