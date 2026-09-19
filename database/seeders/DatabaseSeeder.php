<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

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
        ]);

        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
            ]
        );
        $superAdmin->assignRole('Super Admin');

        // Create accounts for permanent HQ employees so they can log in without registering.
        // SUPERADMIN is intentionally excluded — reserved for testing the register flow.
        foreach (['CEO', 'COO', 'CHRO', 'CFO', 'CMO'] as $code) {
            $employee = Employee::where('employee_code', $code)->first();
            if (! $employee) {
                continue;
            }

            $user = User::firstOrCreate(
                ['email' => strtolower($code).'@mindsia.id'],
                [
                    'name' => $employee->full_name,
                    'password' => Hash::make('password'),
                    'employee_id' => $employee->id,
                    'is_active' => true,
                ]
            );

            if (! $user->employee_id) {
                $user->update(['employee_id' => $employee->id]);
            }

            $employee->load('currentStatus.position.role');
            $role = $employee->currentStatus?->position?->role;
            if ($role) {
                $user->syncRoles([$role]);
            }
        }
    }
}
