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
            // Organization domain
            'organization.province.view' => 'Lihat provinsi',
            'organization.province.create' => 'Buat provinsi',
            'organization.province.update' => 'Ubah provinsi',
            'organization.province.delete' => 'Hapus provinsi',

            'organization.region.view' => 'Lihat region',
            'organization.region.create' => 'Buat region',
            'organization.region.update' => 'Ubah region',
            'organization.region.delete' => 'Hapus region',

            'organization.area.view' => 'Lihat area',
            'organization.area.create' => 'Buat area',
            'organization.area.update' => 'Ubah area',
            'organization.area.delete' => 'Hapus area',

            'organization.branch.view' => 'Lihat cabang',
            'organization.branch.create' => 'Buat cabang',
            'organization.branch.update' => 'Ubah cabang',
            'organization.branch.delete' => 'Hapus cabang',
            'organization.branch.toggle_active' => 'Aktifkan/nonaktifkan cabang',

            'organization.institution.view' => 'Lihat institusi',
            'organization.institution.create' => 'Buat institusi',
            'organization.institution.update' => 'Ubah institusi',
            'organization.institution.delete' => 'Hapus institusi',
        ];

        foreach ($permissions as $name => $description) {
            Permission::firstOrCreate(
                ['name' => $name],
                ['description' => $description]
            );
        }
    }
}
