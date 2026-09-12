<?php

namespace App\Http\Requests\Bonus;

use Illuminate\Foundation\Http\FormRequest;

class StoreMarketingBonusRuleTierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('BOARD_OF_DIRECTORS');
    }

    public function rules(): array
    {
        return [
            'minimum_tenure_months' => ['required', 'integer', 'min:0'],
            'maximum_tenure_months' => ['nullable', 'integer', 'min:0'],
            'minimum_achievement_percentage' => ['required', 'numeric', 'min:0'],
            'maximum_achievement_percentage' => ['nullable', 'numeric', 'min:0'],
            'reward_type' => ['required', 'in:percentage,fixed'],
            'reward_value' => ['required', 'numeric', 'min:0'],
        ];
    }
}
