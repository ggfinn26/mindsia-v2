<?php

namespace Tests\Feature\Attendance;

use App\Models\AttendanceRule;
use App\Models\AttendanceRuleActionExecution;
use App\Models\AttendanceRulePayrollAction;
use App\Models\AttendanceRuleViolation;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\EmployeeCompensation;
use App\Models\EmployeePayroll;
use App\Models\EmployeeWorkAttendanceLog;
use App\Models\PayrollComponent;
use App\Models\PayrollPeriod;
use App\Models\User;
use App\Models\WorkScheduleAssignment;
use App\Models\WorkScheduleDay;
use App\Models\WorkScheduleRule;
use App\Services\Payroll\PayrollGenerationService;
use Carbon\Carbon;
use Tests\TestCase;

// Perubahan aturan absensi lewat UI (buat, ubah nilai/operator/jenis, tambah/kurangi aksi, nonaktifkan, hapus) harus
// langsung tercermin di pelanggaran dan potongan gaji. "Hari ini" dibekukan 1 Okt 2026; absensi September.
class AttendanceRuleUiTest extends TestCase
{
    private User $hr;

    private Employee $employee;

    private PayrollComponent $deduction;

    protected function setUp(): void
    {
        parent::setUp();

        $this->travelTo(Carbon::create(2026, 10, 1, 9, 0));
        $this->hr = User::factory()->create();
        $this->hr->givePermissionTo('attendance.rule.manage');

        $branch = Branch::factory()->create();
        $this->employee = Employee::factory()->create(['branch_id' => $branch->id, 'is_active' => true, 'join_date' => '2024-01-01']);
        $rule = WorkScheduleRule::factory()->create(['is_required' => true, 'is_active' => true]);
        foreach ([1, 2, 3, 4, 5, 6] as $day) {
            WorkScheduleDay::create(['work_schedule_rule_id' => $rule->id, 'day_of_week' => $day]);
        }
        WorkScheduleAssignment::create(['work_schedule_rule_id' => $rule->id, 'assignable_type' => Employee::class, 'assignable_id' => $this->employee->id, 'effective_start_date' => '2026-01-01', 'is_active' => true]);

        $this->deduction = PayrollComponent::firstOrCreate(['component_code' => 'LATE_DEDUCTION'], ['component_name' => 'Potongan Terlambat', 'component_type' => 'deduction', 'calculation_method' => 'manual', 'is_active' => true]);

        // Terlambat total 35 menit di September
        foreach ([['2026-09-01', 20], ['2026-09-02', 15]] as [$date, $late]) {
            EmployeeWorkAttendanceLog::factory()->create(['employee_id' => $this->employee->id, 'branch_id' => $branch->id, 'attendance_date' => $date, 'status' => 'present', 'late_minutes' => $late]);
        }
    }

    private function payload(array $overrides = [], ?array $actions = null): array
    {
        return array_merge([
            'rule_name' => 'Terlambat '.uniqid(), 'attendance_type' => 'work_schedule', 'trigger_type' => 'monthly_late_minutes',
            'trigger_operator' => '>=', 'trigger_value' => 30, 'period_type' => 'monthly', 'is_active' => 1,
            'actions' => $actions ?? [$this->deductionAction()],
        ], $overrides);
    }

    private function deductionAction(array $overrides = [], int $order = 1): array
    {
        return array_merge([
            'action_type' => 'payroll_deduction', 'action_order' => $order,
            'payroll_component_id' => $this->deduction->id, 'deduction_type' => 'per_minute', 'deduction_value' => 1000,
        ], $overrides);
    }

    private function createRule(array $overrides = [], ?array $actions = null): AttendanceRule
    {
        $this->actingAs($this->hr)->post(route('attendance-rules.store'), $this->payload($overrides, $actions))->assertSessionHasNoErrors();

        return AttendanceRule::latest('id')->firstOrFail();
    }

    private function update(AttendanceRule $rule, array $overrides = [], ?array $actions = null)
    {
        $payload = array_merge($this->payload([], $actions), [
            'rule_name' => $rule->rule_name, 'trigger_value' => (float) $rule->trigger_value, 'trigger_operator' => $rule->trigger_operator,
            'trigger_type' => $rule->trigger_type, 'attendance_type' => $rule->attendance_type, 'is_active' => (int) $rule->is_active,
        ], $overrides);

        return $this->actingAs($this->hr)->put(route('attendance-rules.update', $rule), $payload);
    }

    private function violations(AttendanceRule $rule)
    {
        return AttendanceRuleViolation::where('attendance_rule_id', $rule->id)->where('employee_id', $this->employee->id)->get();
    }

    // ---- membuat aturan ----------------------------------------------------------------------------------------------

    public function test_aturan_baru_beserta_detail_potongan_tersimpan_dan_langsung_menghasilkan_pelanggaran(): void
    {
        $rule = $this->createRule();

        $action = $rule->actions()->with('payrollAction')->sole();
        $this->assertSame(
            ['payroll_deduction', $this->deduction->id, 'per_minute', 1000.0],
            [$action->action_type, $action->payrollAction->payroll_component_id, $action->payrollAction->deduction_type, (float) $action->payrollAction->deduction_value],
        );
        $this->assertSame(35.0, (float) $this->violations($rule)->sole()->trigger_value, 'Tanpa menunggu job harian.');
    }

    public function test_aksi_non_potongan_tidak_menyimpan_detail_potongan(): void
    {
        $rule = $this->createRule(actions: [['action_type' => 'notification', 'action_order' => 1], ['action_type' => 'warning_letter', 'action_order' => 2]]);

        $this->assertSame(0, AttendanceRulePayrollAction::whereIn('attendance_rule_action_id', $rule->actions()->pluck('id'))->count());
        $this->assertSame(['pending', 'pending'], $this->violations($rule)->sole()->actionExecutions()->pluck('status')->all());
    }

    public function test_potongan_gaji_tanpa_detail_ditolak_dan_tidak_ada_yang_tersimpan(): void
    {
        $before = AttendanceRule::count();

        foreach ([
            ['payroll_component_id' => null],
            ['deduction_type' => null],
            ['deduction_value' => null],
            ['deduction_type' => 'percentage', 'deduction_value' => 150],
            ['deduction_value' => -5],
        ] as $broken) {
            $this->actingAs($this->hr)->post(route('attendance-rules.store'), $this->payload(actions: [$this->deductionAction($broken)]))->assertSessionHasErrors();
        }

        $this->assertSame($before, AttendanceRule::count());
    }

    public function test_komponen_yang_bukan_potongan_ditolak(): void
    {
        $earning = PayrollComponent::firstOrCreate(['component_code' => 'BASE_SALARY'], ['component_name' => 'Gaji Pokok', 'component_type' => 'earning', 'calculation_method' => 'fixed', 'is_active' => true]);

        $this->actingAs($this->hr)->post(route('attendance-rules.store'), $this->payload(actions: [$this->deductionAction(['payroll_component_id' => $earning->id])]))
            ->assertSessionHasErrors('actions.0.payroll_component_id');
    }

    public function test_tanpa_izin_ditolak(): void
    {
        $this->actingAs(User::factory()->create())->post(route('attendance-rules.store'), $this->payload())->assertForbidden();
    }

    // ---- mengubah nilai, operator, dan jenis ------------------------------------------------------------------------------

    public function test_menaikkan_ambang_langsung_menghapus_pelanggaran_yang_tidak_lagi_terpenuhi(): void
    {
        $rule = $this->createRule();
        $this->assertCount(1, $this->violations($rule));

        $this->update($rule, ['trigger_value' => 60])->assertSessionHasNoErrors();

        $this->assertCount(0, $this->violations($rule));
    }

    public function test_menurunkan_ambang_langsung_membuat_pelanggaran_baru(): void
    {
        $rule = $this->createRule(['trigger_value' => 60]);
        $this->assertCount(0, $this->violations($rule));

        $this->update($rule, ['trigger_value' => 30]);

        $this->assertSame(35.0, (float) $this->violations($rule)->sole()->trigger_value);
    }

    public function test_mengganti_jenis_pemicu_mengubah_pelanggaran(): void
    {
        $rule = $this->createRule();   // menit terlambat: 35

        $this->update($rule, ['trigger_type' => 'monthly_late_count', 'trigger_value' => 2]);   // jumlah terlambat: 2

        $this->assertSame(2.0, (float) $this->violations($rule)->sole()->trigger_value);
    }

    public function test_mengganti_operator_langsung_berlaku(): void
    {
        $rule = $this->createRule();   // >= 30: terpenuhi

        $this->update($rule, ['trigger_operator' => '<', 'trigger_value' => 30]);   // 35 < 30: tidak

        $this->assertCount(0, $this->violations($rule));
    }

    public function test_mengganti_tipe_absensi_ke_sesi_menghapus_pelanggaran_kerja(): void
    {
        $rule = $this->createRule();

        $this->update($rule, ['attendance_type' => 'session']);

        $this->assertCount(0, $this->violations($rule), 'Pegawai ini tidak punya sesi.');
    }

    // ---- aksi: tambah, kurangi, ubah detail ------------------------------------------------------------------------------------

    public function test_mengedit_aturan_yang_sudah_punya_pelanggaran_tidak_error_dan_aksi_dipertahankan(): void
    {
        $rule = $this->createRule();
        $actionId = $rule->actions()->value('id');
        $executionId = AttendanceRuleActionExecution::where('attendance_rule_action_id', $actionId)->value('id');

        $this->update($rule, ['rule_name' => 'Nama Baru'])->assertSessionHasNoErrors()->assertRedirect(route('attendance-rules.index'));

        $this->assertSame($actionId, $rule->actions()->value('id'), 'Aksi yang sama tidak dihapus-dibuat ulang.');
        $this->assertSame(1, AttendanceRuleActionExecution::where('id', $executionId)->count());
        $this->assertNotNull(AttendanceRulePayrollAction::where('attendance_rule_action_id', $actionId)->first(), 'Detail potongan tidak ikut terhapus.');
    }

    public function test_mengubah_nilai_potongan_memperbarui_detail_di_tempat(): void
    {
        $rule = $this->createRule();
        $actionId = $rule->actions()->value('id');

        $this->update($rule, actions: [$this->deductionAction(['deduction_type' => 'fixed_amount', 'deduction_value' => 75000])]);

        $config = AttendanceRulePayrollAction::where('attendance_rule_action_id', $actionId)->sole();
        $this->assertSame(['fixed_amount', 75000.0], [$config->deduction_type, (float) $config->deduction_value]);
    }

    public function test_menambah_aksi_langsung_membuat_eksekusi_untuk_pelanggaran_yang_ada(): void
    {
        $rule = $this->createRule();

        $this->update($rule, actions: [$this->deductionAction(), ['action_type' => 'warning_letter', 'action_order' => 2]]);

        $this->assertEqualsCanonicalizing(['pending', 'pending'], $this->violations($rule)->sole()->actionExecutions()->pluck('status')->all());
        $this->assertSame(2, $rule->actions()->count());
    }

    public function test_mengurangi_aksi_menghapus_eksekusinya_tanpa_error(): void
    {
        $rule = $this->createRule(actions: [$this->deductionAction(), ['action_type' => 'warning_letter', 'action_order' => 2]]);
        $this->assertSame(2, $this->violations($rule)->sole()->actionExecutions()->count());

        $this->update($rule, actions: [$this->deductionAction()])->assertSessionHasNoErrors();

        $this->assertSame(1, $this->violations($rule)->sole()->actionExecutions()->count());
        $this->assertSame(1, $rule->actions()->count());
    }

    public function test_mengganti_aksi_potongan_menjadi_notifikasi_menghapus_detail_potongan(): void
    {
        $rule = $this->createRule();

        $this->update($rule, actions: [['action_type' => 'notification', 'action_order' => 1]])->assertSessionHasNoErrors();

        $this->assertSame(0, AttendanceRulePayrollAction::whereIn('attendance_rule_action_id', $rule->actions()->pluck('id'))->count());
    }

    // ---- nonaktifkan, hapus ------------------------------------------------------------------------------------------------------

    public function test_menonaktifkan_aturan_langsung_membersihkan_pelanggaran_dan_mengaktifkan_kembali_mengembalikannya(): void
    {
        $rule = $this->createRule();

        $this->update($rule, ['is_active' => 0]);
        $this->assertCount(0, $this->violations($rule));

        $this->update($rule->fresh(), ['is_active' => 1]);
        $this->assertCount(1, $this->violations($rule));
    }

    public function test_aturan_yang_sudah_pernah_dilanggar_tidak_bisa_dihapus_tetapi_yang_belum_bisa(): void
    {
        $fired = $this->createRule();
        $quiet = $this->createRule(['trigger_value' => 9999]);

        $this->actingAs($this->hr)->delete(route('attendance-rules.destroy', $fired))->assertSessionHasErrors();
        $this->actingAs($this->hr)->delete(route('attendance-rules.destroy', $quiet))->assertRedirect();

        $this->assertSame([1, 0], [AttendanceRule::where('id', $fired->id)->count(), AttendanceRule::where('id', $quiet->id)->count()]);
    }

    // ---- halaman ---------------------------------------------------------------------------------------------------------------------

    public function test_halaman_buat_dan_ubah_menampilkan_komponen_potongan_dan_nilai_tersimpan(): void
    {
        $rule = $this->createRule();

        $this->actingAs($this->hr)->get(route('attendance-rules.create'))->assertOk()->assertSee('Potongan Terlambat')->assertSee('Komponen Potongan');
        $this->actingAs($this->hr)->get(route('attendance-rules.edit', $rule))->assertOk()->assertSee('Potongan Terlambat')->assertSee('per_minute');
    }

    // ---- sampai ke slip gaji ------------------------------------------------------------------------------------------------------------

    public function test_aturan_dari_ui_memotong_gaji_dan_perubahan_nilai_berlaku_di_payroll_berikutnya(): void
    {
        $base = PayrollComponent::firstOrCreate(['component_code' => 'BASE_SALARY'], ['component_name' => 'Gaji Pokok', 'component_type' => 'earning', 'calculation_method' => 'fixed', 'is_active' => true]);
        EmployeeCompensation::create(['employee_id' => $this->employee->id, 'payroll_component_id' => $base->id, 'value' => 5_000_000]);
        $period = PayrollPeriod::create(['period_year' => 2026, 'period_month' => 9, 'status' => 'finalized', 'confirmed_at' => now()]);
        $rule = $this->createRule();   // 35 menit x 1000

        $deductionOf = function () use ($period) {
            app(PayrollGenerationService::class)->generate($period);

            return (float) EmployeePayroll::where('payroll_period_id', $period->id)->where('employee_id', $this->employee->id)->firstOrFail()
                ->items()->where('component_code_snapshot', 'LATE_DEDUCTION')->sum('total_amount');
        };

        $this->assertSame(35_000.0, $deductionOf());

        $this->update($rule, actions: [$this->deductionAction(['deduction_value' => 2000])]);
        $this->assertSame(70_000.0, $deductionOf(), 'Tarif baru berlaku saat payroll di-generate ulang.');

        $this->update($rule, ['is_active' => 0]);
        $this->assertSame(0.0, $deductionOf(), 'Aturan nonaktif tidak lagi memotong.');
    }
}
