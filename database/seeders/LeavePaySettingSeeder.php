<?php

namespace Database\Seeders;

use App\Models\LeavePaySetting;
use Illuminate\Database\Seeder;

class LeavePaySettingSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['permission', 'sick', 'leave'] as $type) {
            LeavePaySetting::firstOrCreate(
                ['leave_type' => $type],
                ['is_paid' => true]
            );
        }
    }
}
