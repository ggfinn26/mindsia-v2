<?php

namespace Database\Factories;

use App\Models\KpiBonusRule;
use App\Models\KpiBonusRuleTier;
use Illuminate\Database\Eloquent\Factories\Factory;

class KpiBonusRuleTierFactory extends Factory
{
    protected $model = KpiBonusRuleTier::class;

    public function definition(): array
    {
        return [
            'kpi_bonus_rule_id' => KpiBonusRule::factory(),
            'minimum_score' => $this->faker->randomFloat(2, 0, 50),
            'maximum_score' => $this->faker->randomFloat(2, 51, 100),
            'reward_type' => $this->faker->randomElement(['percentage', 'fixed']),
            'reward_value' => $this->faker->randomFloat(2, 100, 5000),
        ];
    }

    public function forRule(int $ruleId): self
    {
        return $this->state(fn () => ['kpi_bonus_rule_id' => $ruleId]);
    }
}
