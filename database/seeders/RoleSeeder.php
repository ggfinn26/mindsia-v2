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
            'Super Admin',
            'Board',
            'CEO',
            'IT',
            'HR',
            'HRR',
            'Finance',
            'Manager Area',
            'Operasional',
            'Marketing',
            'Tutor',
            'REGULAR_TUTOR',
        ];

        foreach ($roles as $name) {
            Role::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $allPermissions = Permission::pluck('name')->toArray();

        // Developer / Super Admin / CEO have all permissions
        foreach (['Developer', 'Super Admin', 'CEO'] as $fullAccessRole) {
            Role::where('name', $fullAccessRole)->first()
                ?->syncPermissions($allPermissions);
        }

        // IT: system management only
        $itPermissions = [
            'system.permission.manage',
            'system.api_key.create',
            'system.api_key.update',
            'system.webhook.view',
            'system.webhook.manage',
            'system.bot.manage',
            'system.notification_routing.manage',
            'auth.user.force_reset_password',
            'notification.template.create',
            'notification.template.update',
            'notification.template.delete',
            'notification.variable.create',
            'notification.variable.update',
            'notification.variable.delete',
            'notification.send_manual',
            'notification.log.view',
            'notification.log.resend',
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

        // Survey: CHRO (Board+HR) + CMO (Board+Marketing) kelola; Manager Area lihat hasil
        $surveyManagePerms = [
            'survey.form.view',
            'survey.form.create',
            'survey.form.update',
            'survey.form.delete',
            'survey.assignment.create',
            'survey.result.view',
        ];
        foreach ($surveyManagePerms as $perm) {
            if (in_array($perm, $allPermissions, true)) {
                foreach (['Board', 'HR', 'Marketing'] as $roleName) {
                    Role::where('name', $roleName)->first()
                        ?->givePermissionTo($perm);
                }
            }
        }
        if (in_array('survey.result.view', $allPermissions, true)) {
            Role::where('name', 'Manager Area')->first()
                ?->givePermissionTo('survey.result.view');
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
