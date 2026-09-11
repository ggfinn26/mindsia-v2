<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $permissions = [
            'organization.province.view',
            'organization.province.create',
            'organization.province.update',
            'organization.province.delete',
            'organization.region.view',
            'organization.region.create',
            'organization.region.update',
            'organization.region.delete',
            'organization.area.view',
            'organization.area.create',
            'organization.area.update',
            'organization.area.delete',
            'organization.branch.view',
            'organization.branch.create',
            'organization.branch.update',
            'organization.branch.delete',
            'organization.branch.toggle_active',
            'organization.institution.view',
            'organization.institution.create',
            'organization.institution.update',
            'organization.institution.delete',
            'curriculum.program.view',
            'curriculum.program.create',
            'curriculum.program.update',
            'curriculum.program.delete',
            'curriculum.quota.view',
            'curriculum.quota.create',
            'curriculum.quota.update',
            'curriculum.quota.delete',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name]);
        }
    }
}
