<?php

namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class PositionSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // [position_name, primary_role, hierarchy_order]
        // Double-division positions use their primary (first-listed) division as role.
        $positions = [
            // Divisi IT
            ['Super Admin',                     'IT',           2],

            // Divisi Developer
            ['Developer',                       'Developer',    1],

            // Divisi Board (primary); some double to HR/Finance/Operasional
            ['Chief Executive Officer',         'Board',        1],
            ['Chief Operating Officer',         'Board',        1],
            ['Chief Human Resources Officer',   'Board',        1],
            ['Chief Product Officer',           'Board',        1],
            ['Chief Financial Officer',         'Board',        1],
            ['Chief Marketing Officer',         'Board',        1],

            // Divisi HR
            ['Human Resources Personalia',      'HR',           2],
            ['Human Resources Recruitment',     'HR',           2],

            // Divisi Manager Area
            ['Manager Area',                    'Manager Area', 3],
            ['Person In Charge',                'Manager Area', 3],

            // Divisi Marketing
            ['Marketing',                       'Marketing',    4],
            ['Executive Assistant',             'Marketing',    4],

            // Divisi Tutor
            ['Official Tutor',                  'Tutor',        4],
            ['Regular Tutor',                   'Tutor',        4],
        ];

        $roleCache = Role::all()->keyBy('name');

        foreach ($positions as [$name, $roleName, $hierarchyOrder]) {
            Position::firstOrCreate(
                ['position_name' => $name],
                [
                    'role_id' => $roleCache->get($roleName)?->id,
                    'hierarchy_order' => $hierarchyOrder,
                ]
            );
        }
    }
}
