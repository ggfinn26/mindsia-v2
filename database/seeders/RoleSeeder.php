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
            'Developer',
            'Board',
            'IT',
            'HR',
            'Finance',
            'Manager Area',
            'Operasional',
            'Marketing',
            'Tutor',
        ];

        foreach ($roles as $name) {
            Role::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $allPermissions = Permission::pluck('name')->toArray();

        // Developer has all permissions — app owner/builder
        Role::where('name', 'Developer')->first()
            ?->syncPermissions($allPermissions);

        // IT: system management only
        $itPermissions = [
            'system.permission.manage',
            'system.api_key.create',
            'system.api_key.update',
            'system.webhook.manage',
            'system.bot.manage',
            'system.notification_routing.manage',
            'auth.user.force_reset_password',
        ];
        Role::where('name', 'IT')->first()
            ?->syncPermissions(array_intersect($itPermissions, $allPermissions));

        // Placement test: Tutor (Official + Regular), Marketing (Official Tutor is in Marketing division),
        // Manager Area (covers Manager Area + PIC positions), Board (CEO)
        $placementTestPermission = 'class.placement_test.manage';
        if (in_array($placementTestPermission, $allPermissions, true)) {
            foreach (['Tutor', 'Marketing', 'Manager Area', 'Board'] as $roleName) {
                Role::where('name', $roleName)->first()
                    ?->givePermissionTo($placementTestPermission);
            }
        }

        // Landing page: Board & Operasional kelola konten publik
        $landingPermissions = [
            'landing.testimonial.manage',
            'landing.photo.manage',
            'landing.leader.manage',
        ];
        foreach ($landingPermissions as $perm) {
            if (in_array($perm, $allPermissions, true)) {
                foreach (['Board', 'Operasional'] as $roleName) {
                    Role::where('name', $roleName)->first()
                        ?->givePermissionTo($perm);
                }
            }
        }
    }
}
