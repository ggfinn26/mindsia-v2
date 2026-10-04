<?php

namespace Database\Seeders;

use App\Models\MarketingKpiRankingRule;
use Illuminate\Database\Seeder;

class MarketingKpiRankingRuleSeeder extends Seeder
{
    public function run(): void
    {
        MarketingKpiRankingRule::firstOrCreate(
            ['rule_code' => 'KPI-DEFAULT'],
            [
                'rule_name' => 'Aturan KPI Marketing Default',
                'revenue_basis' => 'cash_collected',
                'is_active' => true,
                'created_by_employee_id' => null,
            ]
        );
    }
}
