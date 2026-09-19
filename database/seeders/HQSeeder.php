<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Seeder;

class HQSeeder extends Seeder
{
    public function run(): void
    {
        $employee = Employee::firstOrCreate(
            ['employee_code' => 'SUPERADMIN'],
            [
                'full_name' => 'Admin CEO',
                'gender' => 'L',
                'birthdate' => '1990-01-01',
                'email' => 'admin@mindsia.test',
                'whatsapp_number' => '08000000000',
                'is_hq' => true,
                'is_active' => true,
            ]
        );

        // ponytail: no user created — SUPERADMIN employee used for testing register flow
    }
}
