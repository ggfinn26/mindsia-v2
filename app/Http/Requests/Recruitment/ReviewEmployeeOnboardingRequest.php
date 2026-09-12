<?php

namespace App\Http\Requests\Recruitment;

use Illuminate\Foundation\Http\FormRequest;

class ReviewEmployeeOnboardingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('review_onboarding') || $this->user()->hasRole('BOARD');
    }

    public function rules(): array
    {
        return [
            'decision' => ['required', 'in:approved,rejected'],
            'review_notes' => ['nullable', 'string'],
            'rejection_reason' => ['required_if:decision,rejected', 'nullable', 'string'],
        ];
    }
}
