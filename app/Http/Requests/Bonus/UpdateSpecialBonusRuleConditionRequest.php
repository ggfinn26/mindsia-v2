<?php

namespace App\Http\Requests\Bonus;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSpecialBonusRuleConditionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('BOARD_OF_DIRECTORS');
    }

    public function rules(): array
    {
        return [
            'metric_code' => ['required', 'string', 'max:100'],
            'period_type' => ['required', 'string', 'max:50'],
            'operator' => ['required', 'in:>=,<=,=,>,<,!='],
            'target_value' => ['required', 'numeric'],
            'data_source' => ['required', 'string', 'max:100'],
            'is_active' => ['boolean'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
