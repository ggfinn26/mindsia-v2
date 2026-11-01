<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Kebijakan absensi per cabang/area/region dihentikan: wajib absen ditentukan oleh posisi, cabang hanya menyimpan lokasi.
     * Tabel attendance_policies* sengaja tidak dihapus (tidak ada data bermakna dan masih bisa dipulihkan);
     * yang dibersihkan hanya sisa konfigurasi widget dan permission yang tidak punya fitur lagi.
     */
    public function up(): void
    {
        DB::table('dashboard_widget_configs')->where('widget_key', 'compliance_policies')->delete();
        DB::table('user_dashboard_widget_customizations')->where('widget_key', 'compliance_policies')->delete();

        $permissionIds = DB::table('permissions')->whereIn('name', [
            'attendance.policy.index', 'attendance.policy.create', 'attendance.policy.edit', 'dashboard.widget.compliance_policies.view',
        ])->pluck('id');

        DB::table('role_has_permissions')->whereIn('permission_id', $permissionIds)->delete();
        DB::table('model_has_permissions')->whereIn('permission_id', $permissionIds)->delete();
        DB::table('permissions')->whereIn('id', $permissionIds)->delete();
    }

    public function down(): void
    {
        // Data konfigurasi yang dihapus tidak dipulihkan; jalankan PermissionSeeder bila izinnya diperlukan kembali.
    }
};
