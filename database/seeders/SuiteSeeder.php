<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuiteSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the full test-suite seeder.
     *
     * Seeds all data required by the test suite: auth foundations (permissions, roles),
     * organisational structure (provinces, regions, areas, branches, positions),
     * employee records, domain configuration (leave-pay settings, payroll components,
     * dashboard widget configs), and the super-admin user.
     *
     * Runs once via TestCase::$seeder at suite start.
     * DatabaseTransactions in Pest.php preserves this data across every test.
     */
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            BranchDataSeeder::class,
            PositionSeeder::class,
            EmployeeSeeder::class,
            HQSeeder::class,
            LeavePaySettingSeeder::class,
            PayrollComponentSeeder::class,
            DashboardWidgetConfigSeeder::class,
        ]);

        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
            ]
        );
        $superAdmin->assignRole('Super Admin');
    }
}
