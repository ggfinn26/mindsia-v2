<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBranchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('BOARD');
    }

    public function rules(): array
    {
        return [
            'areas_id'           => ['required', 'exists:areas,id'],
            'branch_name'        => ['required', 'string', 'max:150'],
            'code_branches'      => ['required', 'string', 'max:20', 'unique:branches,code_branches'],
            'address'            => ['required', 'string'],
            'gmaps_url'          => ['nullable', 'string', 'max:500'],
            'whatsapp'           => ['required', 'string', 'max:255'],
            'instagram'          => ['nullable', 'string', 'max:255'],
            'latitude'           => ['required', 'numeric', 'between:-90,90'],
            'longitude'          => ['required', 'numeric', 'between:-180,180'],
            'radius_meters'      => ['required', 'integer', 'min:1'],
            'ma_pic_employee_id' => ['nullable', 'exists:employees,id'],
        ];
    }
}
