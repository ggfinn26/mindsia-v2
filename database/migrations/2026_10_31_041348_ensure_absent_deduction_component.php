<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Kebijakan potongan: alpha selalu dipotong lewat ABSENT_DEDUCTION. Bila komponen ini tidak ada,
     * PayrollGenerationService hanya menulis peringatan dan potongan alpha hilang diam-diam, jadi dijamin ada.
     * Baris yang sudah ada (termasuk status aktifnya) tidak diubah agar pilihan admin dihormati.
     */
    public function up(): void
    {
        $now = now();

        DB::table('payroll_components')->insertOrIgnore([
            'component_code' => 'ABSENT_DEDUCTION',
            'component_name' => 'Potongan Absen',
            'component_type' => 'deduction',
            'calculation_method' => 'manual',
            'is_taxable' => false,
            'is_active' => true,
            'is_system' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Cuti kini digaji; komponen ini hanya memotong izin (dan jenis lain yang diatur tidak berbayar per posisi).
        DB::table('payroll_components')
            ->where('component_code', 'LEAVE_DEDUCTION')
            ->where('component_name', 'Potongan Cuti Tak Berbayar')
            ->update(['component_name' => 'Potongan Izin Tak Berbayar', 'updated_at' => $now]);
    }

    public function down(): void
    {
        DB::table('payroll_components')
            ->where('component_code', 'LEAVE_DEDUCTION')
            ->where('component_name', 'Potongan Izin Tak Berbayar')
            ->update(['component_name' => 'Potongan Cuti Tak Berbayar']);
    }
};
