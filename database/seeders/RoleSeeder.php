<?php

namespace Database\Seeders;

use App\Models\Position;
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
            'CPO',
            'CFO',
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

        // HR staff positions — can review leave requests
        $hrStaffPositions = ['Human Resources Personalia', 'Chief Human Resources Officer'];
        Position::whereIn('position_name', $hrStaffPositions)->with('role')->get()
            ->each(fn ($p) => $p->role?->givePermissionTo('attendance.leave.review'));

        // C-Level positions — can approve leave from HR staff
        $cLevelPositions = [
            'Chief Executive Officer',
            'Chief Operating Officer',
            'Chief Human Resources Officer',
            'Chief Financial Officer',
            'Chief Marketing Officer',
            'Chief Product Officer',
        ];
        Position::whereIn('position_name', $cLevelPositions)->with('role')->get()
            ->each(fn ($p) => $p->role?->givePermissionTo('attendance.leave.approve_hr_staff'));
    }
}
