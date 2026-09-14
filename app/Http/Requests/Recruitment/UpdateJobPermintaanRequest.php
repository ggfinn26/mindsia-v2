<?php

namespace App\Http\Requests\Recruitment;

use App\Models\JobPermintaan;
use Illuminate\Foundation\Http\FormRequest;

class UpdateJobPermintaanRequest extends FormRequest
{
    public function authorize(): bool
    {
        // hanya bisa edit saat draft
        return $this->user()->can('recruitment.job_permintaan.update')
            && $this->route('jobPermintaan')->status === JobPermintaan::STATUS_DRAFT;
    }

    public function rules(): array
    {
        return [
            'detail.position_id' => ['sometimes', 'exists:positions,id'],
            'detail.employment_type' => ['sometimes', 'string', 'max:50'],
            'detail.request_type' => ['sometimes', 'in:new_position,replacement'],
            'detail.headcount' => ['sometimes', 'integer', 'min:1'],
            'detail.current_headcount' => ['nullable', 'integer', 'min:0'],
            'detail.job_description' => ['nullable', 'string'],
            'detail.target_start_date' => ['nullable', 'date'],
            'requirements_auto' => ['sometimes', 'array'],
            'requirements_manual' => ['sometimes', 'array'],
            'requirements_manual.*.criteria_name' => ['required_with:requirements_manual', 'string', 'max:100'],
            'requirements_manual.*.criteria_desc' => ['required_with:requirements_manual', 'string'],
        ];
    }
}
