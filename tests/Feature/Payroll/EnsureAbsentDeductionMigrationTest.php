<?php

namespace Tests\Feature\Payroll;

use App\Models\PayrollComponent;
use Illuminate\Database\Migrations\Migration;
use Tests\TestCase;

// Migrasi data yang menjamin potongan alpha tidak hilang diam-diam karena komponennya tidak ada.
class EnsureAbsentDeductionMigrationTest extends TestCase
{
    private function migration(): Migration
    {
        return require glob(database_path('migrations/*_ensure_absent_deduction_component.php'))[0];
    }

    public function test_komponen_alpha_dibuat_bila_hilang_dan_aktif_serta_sistem(): void
    {
        PayrollComponent::where('component_code', 'ABSENT_DEDUCTION')->delete();

        $this->migration()->up();

        $component = PayrollComponent::where('component_code', 'ABSENT_DEDUCTION')->firstOrFail();
        $this->assertSame(['deduction', true, true], [$component->component_type, $component->is_active, $component->is_system]);
    }

    public function test_komponen_alpha_yang_sudah_ada_tidak_diubah_termasuk_status_nonaktif(): void
    {
        PayrollComponent::where('component_code', 'ABSENT_DEDUCTION')->delete();
        PayrollComponent::create(['component_code' => 'ABSENT_DEDUCTION', 'component_name' => 'Nama Kustom', 'component_type' => 'deduction', 'calculation_method' => 'manual', 'is_active' => false]);

        $this->migration()->up();

        $component = PayrollComponent::where('component_code', 'ABSENT_DEDUCTION')->firstOrFail();
        $this->assertSame(['Nama Kustom', false], [$component->component_name, $component->is_active]);
        $this->assertSame(1, PayrollComponent::where('component_code', 'ABSENT_DEDUCTION')->count());
    }

    public function test_label_komponen_izin_diganti_hanya_bila_masih_nama_lama(): void
    {
        PayrollComponent::where('component_code', 'LEAVE_DEDUCTION')->delete();
        PayrollComponent::create(['component_code' => 'LEAVE_DEDUCTION', 'component_name' => 'Potongan Cuti Tak Berbayar', 'component_type' => 'deduction', 'calculation_method' => 'manual', 'is_active' => true]);

        $this->migration()->up();
        $this->assertSame('Potongan Izin Tak Berbayar', PayrollComponent::where('component_code', 'LEAVE_DEDUCTION')->value('component_name'));

        PayrollComponent::where('component_code', 'LEAVE_DEDUCTION')->update(['component_name' => 'Nama Pilihan Admin']);
        $this->migration()->up();
        $this->assertSame('Nama Pilihan Admin', PayrollComponent::where('component_code', 'LEAVE_DEDUCTION')->value('component_name'));
    }

    public function test_dijalankan_dua_kali_tidak_menggandakan_komponen(): void
    {
        PayrollComponent::where('component_code', 'ABSENT_DEDUCTION')->delete();

        $this->migration()->up();
        $this->migration()->up();

        $this->assertSame(1, PayrollComponent::where('component_code', 'ABSENT_DEDUCTION')->count());
    }
}
