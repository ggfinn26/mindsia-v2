<?php

namespace Database\Factories;

use App\Models\AttendancePolicy;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendancePolicyFactory extends Factory
{
    protected $model = AttendancePolicy::class;

    public function definition(): array
    {
        return [
            'policy_name' => $this->faker->words(2, true),
            'attendance_scope' => 'branch',
            'branch_id' => null,
            'area_id' => null,
            'region_id' => null,
            'is_attendance_exempt' => false,
            'exemption_reason' => null,
            'is_active' => true,
        ];
    }
}
