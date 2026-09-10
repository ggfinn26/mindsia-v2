<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAreaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('BOARD');
    }

    public function rules(): array
    {
        return [
            'region_id' => ['required', 'exists:regions,id'],
            'name'      => ['required', 'string', 'max:100', 'unique:areas,name,' . $this->route('area')->id],
        ];
    }
}
