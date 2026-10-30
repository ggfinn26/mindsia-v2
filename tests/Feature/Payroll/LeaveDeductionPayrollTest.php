<?php

namespace Tests\Feature\Payroll;

use App\Models\Employee;
use App\Models\EmployeeAttendanceMonthlyRecap;
use App\Models\EmployeeCompensation;
use App\Models\EmployeePayroll;
use App\Models\EmploymentStatus;
use App\Models\LeavePaySetting;
use App\Models\PayrollComponent;
use App\Models\PayrollItem;
use App\Models\PayrollPeriod;
use App\Models\Position;
use App\Models\User;
use App\Services\Payroll\PayrollGenerationService;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

// Potongan gaji untuk izin/sakit/cuti tidak berbayar: setting berlaku PER POSISI, potongan = gaji pokok / hari terjadwal
// x hari tidak berbayar, tidak pernah melebihi gaji pokok, dan Board tidak dipotong.
class LeaveDeductionPayrollTest extends TestCase
{
    private static int $seq = 0;

    private PayrollPeriod $period;

    private PayrollComponent $baseSalary;

    protected function setUp(): void
    {
        parent::setUp();

        $seq = ++self::$seq;
        $this->period = PayrollPeriod::create([
            'period_year' => 2060 + intdiv($seq, 12),
            'period_month' => ($seq % 12) + 1,
            'status' => 'finalized',
            'confirmed_at' => now(),
        ]);

        $this->baseSalary = PayrollComponent::firstOrCreate(
            ['component_code' => 'BASE_SALARY'],
            ['component_name' => 'Gaji Pokok', 'component_type' => 'earning', 'calculation_method' => 'fixed', 'is_active' => true],
        );
        PayrollComponent::firstOrCreate(
            ['component_code' => 'ABSENT_DEDUCTION'],
            ['component_name' => 'Potongan Alpha', 'component_type' => 'deduction', 'calculation_method' => 'manual', 'is_active' => true],
        );
        PayrollComponent::firstOrCreate(
            ['component_code' => 'LEAVE_DEDUCTION'],
            ['component_name' => 'Potongan Izin', 'component_type' => 'deduction', 'calculation_method' => 'manual', 'is_active' => true],
        );
    }

    private function position(string $name = 'Staff'): Position
    {
        return Position::create(['position_name' => $name.uniqid(), 'hierarchy_order' => 5]);
    }

    private function employee(Position $position, array $recap, float $salary = 2_600_000, ?string $role = null): Employee
    {
        $employee = Employee::factory()->create(['is_active' => true]);

        EmploymentStatus::create(['employees_id' => $employee->id, 'position_id' => $position->id, 'type_employment' => 'permanent']);
        EmployeeCompensation::create(['employee_id' => $employee->id, 'payroll_component_id' => $this->baseSalary->id, 'value' => $salary]);

        EmployeeAttendanceMonthlyRecap::create(array_merge([
            'employee_id' => $employee->id,
            'period_year' => $this->period->period_year,
            'period_month' => $this->period->period_month,
            'total_scheduled_working_days' => 26,
            'total_effective_working_days' => 20,
            'total_present' => 20,
        ], $recap));

        if ($role) {
            $user = User::factory()->create(['employee_id' => $employee->id]);
            $user->assignRole(Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']));
        }

        return $employee;
    }

    private function unpaid(Position $position, string $type): void
    {
        LeavePaySetting::create(['position_id' => $position->id, 'leave_type' => $type, 'is_paid' => false]);
    }

    private function generate(): void
    {
        app(PayrollGenerationService::class)->generate($this->period);
    }

    private function deduction(Employee $employee): ?PayrollItem
    {
        $payroll = EmployeePayroll::where('payroll_period_id', $this->period->id)->where('employee_id', $employee->id)->first();

        return $payroll?->items()->where('source_type', 'leave')->first();
    }

    private function alphaDeduction(Employee $employee): ?PayrollItem
    {
        $payroll = EmployeePayroll::where('payroll_period_id', $this->period->id)->where('employee_id', $employee->id)->first();

        return $payroll?->items()->where('component_code_snapshot', 'ABSENT_DEDUCTION')->first();
    }

    private function payrollOf(Employee $employee): EmployeePayroll
    {
        return EmployeePayroll::where('payroll_period_id', $this->period->id)->where('employee_id', $employee->id)->firstOrFail();
    }

    public function test_sakit_tidak_berbayar_dipotong_per_hari_terjadwal(): void
    {
        $position = $this->position();
        $this->unpaid($position, 'sick');
        $employee = $this->employee($position, ['total_sick' => 2]);

        $this->generate();

        $item = $this->deduction($employee);
        $this->assertNotNull($item);
        $this->assertEquals(100_000.0, (float) $item->unit_value);   // 2.600.000 / 26
        $this->assertEquals(200_000.0, (float) $item->total_amount); // 2 hari
    }

    public function test_kebijakan_bawaan_sakit_dan_cuti_digaji_izin_dipotong(): void
    {
        // Posisi tanpa pengaturan sama sekali
        $employee = $this->employee($this->position(), ['total_sick' => 3, 'total_permission' => 2, 'total_leave' => 5]);

        $this->generate();

        $item = $this->deduction($employee);
        $this->assertEquals([2, 200_000.0], [(int) $item->quantity, (float) $item->total_amount], 'Hanya 2 hari izin yang dipotong.');
        $this->assertEquals(2_400_000.0, (float) $this->payrollOf($employee)->net_amount);
    }

    public function test_sakit_dan_cuti_tanpa_izin_tidak_ada_potongan_sama_sekali(): void
    {
        $employee = $this->employee($this->position(), ['total_sick' => 10, 'total_leave' => 10]);

        $this->generate();

        $this->assertNull($this->deduction($employee));
        $this->assertEquals(2_600_000.0, (float) $this->payrollOf($employee)->net_amount);
    }

    public function test_setting_posisi_lain_tidak_memotong_gaji_pegawai_ini(): void
    {
        $other = $this->position('Posisi Lain');
        $this->unpaid($other, 'sick');   // sakit tidak berbayar HANYA untuk posisi lain
        $employee = $this->employee($this->position(), ['total_sick' => 3]);

        $this->generate();

        $this->assertNull($this->deduction($employee), 'Setting posisi lain bocor ke pegawai ini.');
    }

    public function test_setiap_posisi_memakai_setting_miliknya_sendiri(): void
    {
        $paid = $this->position('Berbayar');
        $unpaid = $this->position('Tidak Berbayar');
        $this->unpaid($unpaid, 'leave');

        $employeePaid = $this->employee($paid, ['total_leave' => 2]);
        $employeeUnpaid = $this->employee($unpaid, ['total_leave' => 2]);

        $this->generate();

        $this->assertNull($this->deduction($employeePaid));
        $this->assertEquals(200_000.0, (float) $this->deduction($employeeUnpaid)->total_amount);
    }

    public function test_hanya_jenis_yang_tidak_berbayar_yang_dihitung(): void
    {
        $position = $this->position();
        $this->unpaid($position, 'sick');
        LeavePaySetting::create(['position_id' => $position->id, 'leave_type' => 'leave', 'is_paid' => true]);
        LeavePaySetting::create(['position_id' => $position->id, 'leave_type' => 'permission', 'is_paid' => true]);
        $employee = $this->employee($position, ['total_sick' => 1, 'total_leave' => 5, 'total_permission' => 4]);

        $this->generate();

        $this->assertEquals(100_000.0, (float) $this->deduction($employee)->total_amount); // hanya 1 hari sakit
    }

    public function test_tiga_jenis_tidak_berbayar_dijumlahkan(): void
    {
        $position = $this->position();
        foreach (['sick', 'permission', 'leave'] as $type) {
            $this->unpaid($position, $type);
        }
        $employee = $this->employee($position, ['total_sick' => 1, 'total_permission' => 2, 'total_leave' => 3]);

        $this->generate();

        $item = $this->deduction($employee);
        $this->assertEquals(6, (int) $item->quantity);
        $this->assertEquals(600_000.0, (float) $item->total_amount);
    }

    public function test_potongan_tidak_pernah_melebihi_gaji_pokok(): void
    {
        $position = $this->position();
        $this->unpaid($position, 'leave');
        // Rekap basi/salah: 40 hari cuti padahal hanya 26 hari terjadwal
        $employee = $this->employee($position, ['total_leave' => 40]);

        $this->generate();

        $item = $this->deduction($employee);
        $this->assertLessThanOrEqual(2_600_000.0, (float) $item->total_amount);
        $this->assertEquals(26, (int) $item->quantity);
    }

    public function test_tanpa_hari_terjadwal_tidak_ada_potongan_dan_tidak_error(): void
    {
        $position = $this->position();
        $this->unpaid($position, 'sick');
        $employee = $this->employee($position, ['total_scheduled_working_days' => 0, 'total_sick' => 2]);

        $this->generate();

        $this->assertNull($this->deduction($employee));
        $this->assertNotNull(EmployeePayroll::where('employee_id', $employee->id)->first());
    }

    public function test_board_tidak_pernah_dipotong(): void
    {
        $position = $this->position();
        $this->unpaid($position, 'sick');
        $employee = $this->employee($position, ['total_sick' => 5], role: 'CEO');

        $this->generate();

        $this->assertNull($this->deduction($employee));
    }

    public function test_gaji_pokok_nol_tidak_menghasilkan_potongan(): void
    {
        $position = $this->position();
        $this->unpaid($position, 'sick');
        $employee = $this->employee($position, ['total_sick' => 2], salary: 0);

        $this->generate();

        $this->assertNull($this->deduction($employee));
    }

    // ---- alpha: selalu dipotong per hari ------------------------------------------------------------------------

    public function test_alpha_dipotong_per_hari_dalam_baris_terpisah(): void
    {
        $employee = $this->employee($this->position(), ['total_absent' => 3]);

        $this->generate();

        $item = $this->alphaDeduction($employee);
        $this->assertEquals([3, 100_000.0, 300_000.0, 'attendance'], [(int) $item->quantity, (float) $item->unit_value, (float) $item->total_amount, $item->source_type]);
        $this->assertNull($this->deduction($employee), 'Alpha tidak boleh bercampur dengan baris izin.');
        $this->assertEquals(2_300_000.0, (float) $this->payrollOf($employee)->net_amount);
    }

    public function test_alpha_dan_izin_dipotong_sedangkan_sakit_dan_cuti_digaji(): void
    {
        $employee = $this->employee($this->position(), ['total_absent' => 2, 'total_permission' => 3, 'total_sick' => 4, 'total_leave' => 5]);

        $this->generate();

        $this->assertEquals(200_000.0, (float) $this->alphaDeduction($employee)->total_amount);
        $this->assertEquals(300_000.0, (float) $this->deduction($employee)->total_amount);
        $this->assertEquals(2_100_000.0, (float) $this->payrollOf($employee)->net_amount);
    }

    public function test_alpha_tetap_dipotong_walau_izin_diatur_berbayar(): void
    {
        $position = $this->position();
        LeavePaySetting::create(['position_id' => $position->id, 'leave_type' => 'permission', 'is_paid' => true]);
        $employee = $this->employee($position, ['total_absent' => 1, 'total_permission' => 4]);

        $this->generate();

        $this->assertNull($this->deduction($employee), 'Izin diatur berbayar untuk posisi ini.');
        $this->assertEquals(100_000.0, (float) $this->alphaDeduction($employee)->total_amount);
    }

    public function test_pengaturan_posisi_masih_bisa_menimpa_sakit_dan_cuti_menjadi_tidak_berbayar(): void
    {
        $position = $this->position();
        $this->unpaid($position, 'sick');
        $this->unpaid($position, 'leave');
        $employee = $this->employee($position, ['total_sick' => 1, 'total_leave' => 2]);

        $this->generate();

        $this->assertEquals(300_000.0, (float) $this->deduction($employee)->total_amount);
    }

    public function test_total_hari_dipotong_tidak_melebihi_hari_terjadwal(): void
    {
        // 20 alpha + 10 izin = 30 hari, padahal hanya 26 hari terjadwal: alpha didahulukan, izin dipotong sisa 6 hari
        $employee = $this->employee($this->position(), ['total_absent' => 20, 'total_permission' => 10]);

        $this->generate();

        $this->assertEquals([20, 6], [(int) $this->alphaDeduction($employee)->quantity, (int) $this->deduction($employee)->quantity]);
        $this->assertEquals(0.0, (float) $this->payrollOf($employee)->net_amount);
    }

    public function test_alpha_melebihi_hari_terjadwal_dibatasi_dan_gaji_tidak_negatif(): void
    {
        $employee = $this->employee($this->position(), ['total_absent' => 40]);

        $this->generate();

        $this->assertEquals(26, (int) $this->alphaDeduction($employee)->quantity);
        $this->assertGreaterThanOrEqual(0.0, (float) $this->payrollOf($employee)->net_amount);
    }

    public function test_board_tidak_dipotong_alpha_atau_izin(): void
    {
        $employee = $this->employee($this->position(), ['total_absent' => 3, 'total_permission' => 2], role: 'Board');

        $this->generate();

        $this->assertNull($this->alphaDeduction($employee));
        $this->assertNull($this->deduction($employee));
    }

    public function test_tanpa_hari_terjadwal_atau_gaji_pokok_alpha_tidak_dipotong(): void
    {
        $noSchedule = $this->employee($this->position(), ['total_scheduled_working_days' => 0, 'total_absent' => 3]);
        $noSalary = $this->employee($this->position(), ['total_absent' => 3], salary: 0);

        $this->generate();

        $this->assertNull($this->alphaDeduction($noSchedule));
        $this->assertNull($this->alphaDeduction($noSalary));
    }

    public function test_pembulatan_potongan_dua_desimal(): void
    {
        // 1.000.000 / 26 hari x 3 hari = 115.384,615... -> 115.384,62
        $employee = $this->employee($this->position(), ['total_absent' => 3], salary: 1_000_000);

        $this->generate();

        $item = $this->alphaDeduction($employee);
        $this->assertEquals([38_461.54, 115_384.62], [round((float) $item->unit_value, 2), round((float) $item->total_amount, 2)]);
    }

    public function test_komponen_potongan_alpha_tidak_ada_tidak_membuat_payroll_gagal(): void
    {
        PayrollComponent::where('component_code', 'ABSENT_DEDUCTION')->delete();
        $employee = $this->employee($this->position(), ['total_absent' => 3]);

        $this->generate();   // hanya peringatan di log; payroll tetap terbentuk

        $this->assertNull($this->alphaDeduction($employee));
        $this->assertNotNull(EmployeePayroll::where('employee_id', $employee->id)->first());
    }

    // ---- gaji harian: hari sakit/cuti berbayar ikut dibayar ---------------------------------------------------------

    private function dailyWage(array $recap, float $dailyValue = 100_000): Employee
    {
        $daily = PayrollComponent::firstOrCreate(['component_code' => 'DAILY_SALARY'], ['component_name' => 'Gaji Harian', 'component_type' => 'earning', 'calculation_method' => 'daily', 'is_active' => true]);
        $position = $this->position();
        $employee = $this->employee($position, $recap, salary: 0);
        EmployeeCompensation::create(['employee_id' => $employee->id, 'payroll_component_id' => $daily->id, 'value' => $dailyValue]);

        return $employee;
    }

    public function test_gaji_harian_membayar_hari_hadir_ditambah_sakit_dan_cuti_bukan_izin_atau_alpha(): void
    {
        $employee = $this->dailyWage(['total_effective_working_days' => 10, 'total_present' => 10, 'total_sick' => 2, 'total_leave' => 1, 'total_permission' => 3, 'total_absent' => 4]);

        $this->generate();

        $item = $this->payrollOf($employee)->items()->where('component_code_snapshot', 'DAILY_SALARY')->firstOrFail();
        $this->assertEquals([13, 1_300_000.0], [(int) $item->quantity, (float) $item->total_amount]);
    }

    public function test_gaji_harian_izin_ikut_dibayar_bila_posisi_mengaturnya_berbayar(): void
    {
        $daily = PayrollComponent::firstOrCreate(['component_code' => 'DAILY_SALARY'], ['component_name' => 'Gaji Harian', 'component_type' => 'earning', 'calculation_method' => 'daily', 'is_active' => true]);
        $position = $this->position();
        LeavePaySetting::create(['position_id' => $position->id, 'leave_type' => 'permission', 'is_paid' => true]);
        $employee = $this->employee($position, ['total_effective_working_days' => 10, 'total_present' => 10, 'total_permission' => 3], salary: 0);
        EmployeeCompensation::create(['employee_id' => $employee->id, 'payroll_component_id' => $daily->id, 'value' => 100_000]);

        $this->generate();

        $this->assertEquals(13, (int) $this->payrollOf($employee)->items()->where('component_code_snapshot', 'DAILY_SALARY')->value('quantity'));
    }

    public function test_gaji_harian_hari_dibayar_tidak_melebihi_hari_terjadwal(): void
    {
        $employee = $this->dailyWage(['total_effective_working_days' => 24, 'total_present' => 24, 'total_sick' => 5]);

        $this->generate();

        $this->assertEquals(26, (int) $this->payrollOf($employee)->items()->where('component_code_snapshot', 'DAILY_SALARY')->value('quantity'));
    }

    public function test_gaji_harian_hadir_melebihi_jadwal_tidak_dikurangi(): void
    {
        $employee = $this->dailyWage(['total_effective_working_days' => 28, 'total_present' => 28, 'total_sick' => 2]);

        $this->generate();

        $this->assertEquals(28, (int) $this->payrollOf($employee)->items()->where('component_code_snapshot', 'DAILY_SALARY')->value('quantity'));
    }
}
