<?php

namespace App\Http\Requests\Bonus;

use Illuminate\Foundation\Http\FormRequest;

class StoreKpiBonusRuleTierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('bonus.kpi-rule.create');
    }

    public function rules(): array
    {
        return [
            'minimum_score' => ['required', 'numeric', 'min:0'],
            'maximum_score' => ['nullable', 'numeric', 'min:0'],
            'reward_type' => ['required', 'in:percentage,fixed'],
            'reward_value' => ['required', 'numeric', 'min:0'],
        ];
    }
}
