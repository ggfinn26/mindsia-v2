<?php

namespace Database\Factories;

use App\Models\MarketingBonusRule;
use App\Models\MarketingBonusRuleTier;
use Illuminate\Database\Eloquent\Factories\Factory;

class MarketingBonusRuleTierFactory extends Factory
{
    protected $model = MarketingBonusRuleTier::class;

    public function definition(): array
    {
        return [
            'marketing_bonus_rule_id' => MarketingBonusRule::factory(),
            'minimum_tenure_months' => $this->faker->numberBetween(0, 12),
            'maximum_tenure_months' => $this->faker->numberBetween(13, 60),
            'minimum_achievement_percentage' => $this->faker->randomFloat(2, 0, 50),
            'maximum_achievement_percentage' => $this->faker->randomFloat(2, 51, 100),
            'reward_type' => $this->faker->randomElement(['percentage', 'fixed']),
            'reward_value' => $this->faker->randomFloat(2, 100, 5000),
        ];
    }

    public function forRule(int $ruleId): self
    {
        return $this->state(fn () => ['marketing_bonus_rule_id' => $ruleId]);
    }
}
