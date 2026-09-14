<?php

namespace App\Http\Requests\Marketing;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMarketingKpiRankingRuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('marketing.ranking_rule.manage');
    }

    public function rules(): array
    {
        return [
            'rule_code' => ['required', 'string', 'max:100', Rule::unique('marketing_kpi_ranking_rules', 'rule_code')],
            'rule_name' => ['required', 'string', 'max:255'],
            'revenue_basis' => ['required', 'string', 'max:50'],
        ];
    }
}
