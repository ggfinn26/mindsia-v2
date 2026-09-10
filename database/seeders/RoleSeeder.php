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
        $roles = [
            'BOARD_OF_DIRECTORS' => 'Dewan Direksi',
            'CEO' => 'Chief Executive Officer',
            'COO' => 'Chief Operating Officer',
            'CMO' => 'Chief Marketing Officer',
            'CHRO' => 'Chief Human Resources Officer',
            'OPS_DIRECTOR' => 'Direktur Operasional',
            'FINANCE_DIRECTOR' => 'Direktur Keuangan',
            'MANAGER_AREA' => 'Manager Area',
            'PIC' => 'Person in Charge (Cabang)',
        ];

        $createdRoles = [];
        foreach ($roles as $name => $description) {
            $createdRoles[$name] = Role::firstOrCreate(
                ['name' => $name],
                ['description' => $description]
            );
        }

        $board = $createdRoles['BOARD_OF_DIRECTORS'];
        $allOtherRoles = collect($createdRoles)->except('BOARD_OF_DIRECTORS')->values();

        $allPermissions = Permission::pluck('name')->toArray();
        $viewPermissions = array_filter($allPermissions, fn ($perm) => str_ends_with($perm, '.view'));

        if (! empty($allPermissions)) {
            $board->syncPermissions($allPermissions);
        }

        if (! empty($viewPermissions)) {
            foreach ($allOtherRoles as $role) {
                $role->syncPermissions($viewPermissions);
            }
        }
    }
}
