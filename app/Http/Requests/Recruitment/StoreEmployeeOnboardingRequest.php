<?php

namespace App\Http\Requests\Recruitment;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeOnboardingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('recruitment.onboarding.create');
    }

    public function rules(): array
    {
        return [
            'branch_id' => ['required', 'exists:branches,id'],
            'position_id' => ['required', 'exists:positions,id'],
            'employment_type' => ['required', 'string', 'max:50'],
            'start_date' => ['required', 'date'],
        ];
    }
}
