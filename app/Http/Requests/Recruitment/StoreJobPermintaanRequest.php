<?php

namespace App\Http\Requests\Recruitment;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobPermintaanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('recruitment.job_permintaan.create');
    }

    public function rules(): array
    {
        return [
            'detail.position_id' => ['required', 'exists:positions,id'],
            'detail.employment_type' => ['required', 'string', 'max:50'],
            'detail.request_type' => ['required', 'in:new_position,replacement'],
            'detail.headcount' => ['required', 'integer', 'min:1'],
            'detail.current_headcount' => ['nullable', 'integer', 'min:0'],
            'detail.job_description' => ['nullable', 'string'],
            'detail.target_start_date' => ['nullable', 'date'],
        ];
    }
}
