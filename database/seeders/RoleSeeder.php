<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $roleNames = [
            'Super Admin',
            'CEO',
            'COO',
            'CMO',
            'CHRO',
            'HRR',
            'HRP',
            'DIR_OPS',
            'FINANCE_DIRECTOR',
            'FINANCE_GENERAL',
            'MANAGER_AREA',
            'PIC',
            'MARKETING',
            'REGULAR_TUTOR',
            'OFFICIAL_TUTOR',
        ];

        foreach ($roleNames as $name) {
            Role::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $allPermissions = Permission::pluck('name')->toArray();

        // Assign all permissions to Super Admin
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        if ($superAdminRole) {
            $superAdminRole->syncPermissions($allPermissions);
        }

        // Assign all permissions to CEO (temp until positions are implemented)
        $ceoRole = Role::where('name', 'CEO')->first();
        if ($ceoRole) {
            $ceoRole->syncPermissions($allPermissions);
        }
    }
}
