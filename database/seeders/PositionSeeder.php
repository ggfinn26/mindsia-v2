<?php

namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class PositionSeeder extends Seeder
{
    public function run(): void
    {
        $positions = [
            ['position_name' => 'Super Admin',                   'role' => 'Super Admin',      'hierarchy_order' => 0],
            ['position_name' => 'Chief Executive Officer',       'role' => 'CEO',              'hierarchy_order' => 1],
            ['position_name' => 'Chief Operating Officer',       'role' => 'COO',              'hierarchy_order' => 1],
            ['position_name' => 'Chief Human Resources Officer', 'role' => 'CHRO',             'hierarchy_order' => 1],
            ['position_name' => 'Chief Product Officer',         'role' => 'CPO',              'hierarchy_order' => 1],
            ['position_name' => 'Chief Financial Officer',       'role' => 'CFO',              'hierarchy_order' => 1],
            ['position_name' => 'Chief Marketing Officer',       'role' => 'CMO',              'hierarchy_order' => 1],
            ['position_name' => 'Human Resources Personalia',    'role' => 'HRP',              'hierarchy_order' => 2],
            ['position_name' => 'Human Resources Recruitment',   'role' => 'HRR',              'hierarchy_order' => 2],
            ['position_name' => 'Director Operasional',          'role' => 'DIR_OPS',          'hierarchy_order' => 2],
            ['position_name' => 'Director Finance',              'role' => 'FINANCE_DIRECTOR', 'hierarchy_order' => 2],
            ['position_name' => 'Manager Area',                  'role' => 'MANAGER_AREA',     'hierarchy_order' => 3],
            ['position_name' => 'Person In Charge',              'role' => 'PIC',              'hierarchy_order' => 4],
            ['position_name' => 'Regular Operasional',           'role' => null,               'hierarchy_order' => 4],
            ['position_name' => 'Regular Finance',               'role' => 'FINANCE_GENERAL',  'hierarchy_order' => 4],
            ['position_name' => 'Marketing',                     'role' => 'MARKETING',        'hierarchy_order' => 5],
            ['position_name' => 'Official Tutor',                'role' => 'OFFICIAL_TUTOR',   'hierarchy_order' => 5],
            ['position_name' => 'Executive Assistant',           'role' => null,               'hierarchy_order' => 5],
            ['position_name' => 'Regular Tutor',                 'role' => 'REGULAR_TUTOR',    'hierarchy_order' => 6],
        ];

        $roleCache = Role::all()->keyBy('name');

        foreach ($positions as $data) {
            $roleId = $data['role'] ? $roleCache->get($data['role'])?->id : null;

            Position::firstOrCreate(
                ['position_name' => $data['position_name']],
                ['role_id' => $roleId, 'hierarchy_order' => $data['hierarchy_order']]
            );
        }
    }
}
