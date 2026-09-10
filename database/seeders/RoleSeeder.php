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
            Role::firstOrCreate(['name' => $name]);
        }

        // Temp: CEO gets all permissions until Position domain implemented
        $ceoRole = Role::where('name', 'CEO')->first();
        if ($ceoRole) {
            $allPermissions = Permission::pluck('name')->toArray();
            $ceoRole->syncPermissions($allPermissions);
        }
    }
}
