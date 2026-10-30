<?php

namespace Tests\Feature\Attendance;

use App\Models\AttendanceRule;
use App\Models\AttendanceRuleAction;
use App\Models\AttendanceRuleActionExecution;
use App\Models\AttendanceRulePayrollAction;
use App\Models\AttendanceRuleViolation;
use App\Models\Branch;
use App\Models\ClassRoom;
use App\Models\ClassSchedule;
use App\Models\Employee;
use App\Models\EmployeeCompensation;
use App\Models\EmployeePayroll;
use App\Models\EmployeeSessionAttendanceLog;
use App\Models\EmployeeWorkAttendanceLog;
use App\Models\Holiday;
use App\Models\PayrollComponent;
use App\Models\PayrollPeriod;
use App\Models\Program;
use App\Models\SessionSchedule;
use App\Models\WorkScheduleAssignment;
use App\Models\WorkScheduleDay;
use App\Models\WorkScheduleRule;
use App\Repositories\AttendanceRecapRepository;
use App\Services\Attendance\AttendanceRuleEvaluator;
use App\Services\Payroll\PayrollGenerationService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

// Evaluator aturan absensi (UI Attendance Rules) -> pelanggaran + eksekusi aksi, lalu dipakai payroll.
// Kalender: 1 Sep 2026 = Selasa; Minggu = 6, 13, 20, 27. Jadwal Senin-Sabtu. "Hari ini" dibekukan pada 1 Okt 2026.
class AttendanceRuleEvaluatorTest extends TestCase
{
    private Employee $employee;

    private Branch $branch;

    private AttendanceRuleEvaluator $evaluator;

    private Carbon $september;

    protected function setUp(): void
    {
        parent::setUp();

        $this->travelTo(Carbon::create(2026, 10, 1, 9, 0));
        $this->september = Carbon::create(2026, 9, 1);
        $this->branch = Branch::factory()->create();
        $this->evaluator = app(AttendanceRuleEvaluator::class);
        $this->employee = $this->makeEmployee();
    }

    private function makeEmployee(): Employee
    {
        $employee = Employee::factory()->create(['branch_id' => $this->branch->id, 'is_active' => true, 'join_date' => '2024-01-01']);
        $rule = WorkScheduleRule::factory()->create(['is_required' => true, 'is_active' => true]);
        foreach ([1, 2, 3, 4, 5, 6] as $day) {
            WorkScheduleDay::create(['work_schedule_rule_id' => $rule->id, 'day_of_week' => $day]);
        }
        WorkScheduleAssignment::create([
            'work_schedule_rule_id' => $rule->id, 'assignable_type' => Employee::class, 'assignable_id' => $employee->id,
            'effective_start_date' => '2026-01-01', 'is_active' => true,
        ]);

        return $employee;
    }

    /** @param list<string> $actions action_type */
    private function rule(string $trigger, string $operator, float $value, string $type = 'work_schedule', array $actions = ['notification'], bool $active = true): AttendanceRule
    {
        $rule = AttendanceRule::create([
            'rule_name' => 'Aturan '.uniqid(), 'attendance_type' => $type, 'trigger_type' => $trigger, 'trigger_operator' => $operator,
            'trigger_value' => $value, 'period_type' => $trigger === 'daily_late' ? 'daily' : 'monthly', 'is_active' => $active,
        ]);
        foreach ($actions as $i => $actionType) {
            AttendanceRuleAction::create(['attendance_rule_id' => $rule->id, 'action_type' => $actionType, 'action_order' => $i + 1]);
        }

        return $rule;
    }

    private function log(string $date, string $status = 'present', int $late = 0, ?Employee $employee = null): void
    {
        EmployeeWorkAttendanceLog::factory()->create([
            'employee_id' => ($employee ?? $this->employee)->id, 'branch_id' => $this->branch->id,
            'attendance_date' => $date, 'status' => $status, 'late_minutes' => $late,
        ]);
    }

    private function evaluate(?Employee $employee = null): array
    {
        return $this->evaluator->evaluateEmployee($employee ?? $this->employee, $this->september);
    }

    private function violations(?AttendanceRule $rule = null): Collection
    {
        return AttendanceRuleViolation::where('employee_id', $this->employee->id)
            ->when($rule, fn ($q) => $q->where('attendance_rule_id', $rule->id))
            ->orderBy('period_start_date')->get();
    }

    // ============================ pemicu kerja ============================

    public function test_jumlah_terlambat_bulanan_membuat_pelanggaran_dan_eksekusi_pending_untuk_tiap_aksi(): void
    {
        $rule = $this->rule('monthly_late_count', '>=', 3, actions: ['notification', 'warning_letter', 'payroll_deduction']);
        foreach (['2026-09-01', '2026-09-02', '2026-09-03'] as $date) {
            $this->log($date, late: 10);
        }

        $this->assertSame(['created' => 1, 'updated' => 0, 'removed' => 0], $this->evaluate());

        $violation = $this->violations($rule)->sole();
        $this->assertSame([3.0, '2026-09-01', '2026-09-30'], [(float) $violation->trigger_value, $violation->period_start_date->toDateString(), $violation->period_end_date->toDateString()]);
        $this->assertSame(['pending', 'pending', 'pending'], $violation->actionExecutions()->pluck('status')->all());
    }

    public function test_kurang_dari_ambang_tidak_menjadi_pelanggaran(): void
    {
        $rule = $this->rule('monthly_late_count', '>=', 3);
        $this->log('2026-09-01', late: 10);
        $this->log('2026-09-02', late: 10);

        $this->evaluate();

        $this->assertCount(0, $this->violations($rule));
    }

    public function test_menit_terlambat_bulanan_dijumlahkan(): void
    {
        $rule = $this->rule('monthly_late_minutes', '>=', 30);
        $this->log('2026-09-01', late: 20);
        $this->log('2026-09-02', late: 15);

        $this->evaluate();

        $this->assertSame(35.0, (float) $this->violations($rule)->sole()->trigger_value);
    }

    public function test_alpha_bulanan_dihitung_dari_log_absent(): void
    {
        $rule = $this->rule('monthly_absence', '>=', 2);
        $this->log('2026-09-01', 'absent');
        $this->log('2026-09-02', 'absent');
        $this->log('2026-09-03', 'sick');

        $this->evaluate();

        $this->assertSame(2.0, (float) $this->violations($rule)->sole()->trigger_value);
    }

    public function test_alpha_beruntun_memakai_urutan_hari_kerja_dan_melewati_minggu_serta_libur(): void
    {
        $rule = $this->rule('consecutive_absence', '>=', 3);
        Holiday::create(['holiday_name' => 'Libur', 'holiday_start_date' => '2026-09-10', 'holiday_end_date' => '2026-09-10']);

        // Sabtu 5, (Minggu 6 bukan hari kerja), Senin 7: 2 beruntun
        $this->log('2026-09-05', 'absent');
        $this->log('2026-09-07', 'absent');
        // Rabu 9, (Kamis 10 libur), Jumat 11, Sabtu 12: 3 beruntun
        $this->log('2026-09-09', 'absent');
        $this->log('2026-09-11', 'absent');
        $this->log('2026-09-12', 'absent');

        $this->evaluate();

        $this->assertSame(3.0, (float) $this->violations($rule)->sole()->trigger_value);
    }

    public function test_hari_hadir_atau_izin_memutus_alpha_beruntun(): void
    {
        $rule = $this->rule('consecutive_absence', '>=', 3);
        $this->log('2026-09-01', 'absent');
        $this->log('2026-09-02', 'absent');
        $this->log('2026-09-03', 'sick');       // memutus
        $this->log('2026-09-04', 'absent');
        $this->log('2026-09-05', 'absent');

        $this->evaluate();

        $this->assertCount(0, $this->violations($rule));
    }

    public function test_hari_tanpa_log_juga_memutus_alpha_beruntun(): void
    {
        $rule = $this->rule('consecutive_absence', '>=', 2);
        $this->log('2026-09-01', 'absent');
        $this->log('2026-09-03', 'absent');     // 2 Sep tidak ada log

        $this->evaluate();

        $this->assertCount(0, $this->violations($rule));
    }

    public function test_terlambat_harian_membuat_satu_pelanggaran_per_hari_yang_memenuhi(): void
    {
        $rule = $this->rule('daily_late', '>=', 15);
        $this->log('2026-09-01', late: 20);
        $this->log('2026-09-02', late: 10);     // di bawah ambang
        $this->log('2026-09-03', late: 30);

        $this->evaluate();

        $found = $this->violations($rule);
        $this->assertSame(['2026-09-01', '2026-09-03'], $found->map(fn ($v) => $v->period_start_date->toDateString())->all());
        $this->assertSame([20.0, 30.0], $found->map(fn ($v) => (float) $v->trigger_value)->all());
        $this->assertSame($found[0]->period_start_date->toDateString(), $found[0]->period_end_date->toDateString());
    }

    public function test_semua_operator(): void
    {
        foreach ([['>', 3, false], ['>', 2, true], ['<', 4, true], ['<', 3, false], ['<=', 3, true], ['<=', 2, false], ['=', 3, true], ['=', 2, false], ['!=', 3, false], ['!=', 4, true], ['>=', 3, true], ['>=', 4, false]] as [$operator, $target, $expected]) {
            DB::table('attendance_rule_violations')->where('employee_id', $this->employee->id)->delete();
            $rule = $this->rule('monthly_late_count', $operator, $target);
            foreach (['2026-09-01', '2026-09-02', '2026-09-03'] as $date) {
                EmployeeWorkAttendanceLog::query()->where('employee_id', $this->employee->id)->whereDate('attendance_date', $date)->exists()
                    ?: $this->log($date, late: 5);
            }

            $this->evaluate();

            $this->assertSame($expected, $this->violations($rule)->isNotEmpty(), "Operator {$operator} {$target} dengan 3 keterlambatan.");
            $rule->update(['is_active' => false]);
        }
    }

    public function test_operator_kurang_dari_tidak_menjerat_pegawai_tanpa_kejadian_sama_sekali(): void
    {
        $rule = $this->rule('monthly_late_count', '<=', 5);
        $this->log('2026-09-01');   // hadir tepat waktu

        $this->evaluate();

        $this->assertCount(0, $this->violations($rule), 'Metrik 0 tidak boleh menjadi pelanggaran.');
    }

    // ============================ batas bulan, status, ruang lingkup ============================

    public function test_keterlambatan_bulan_dan_tahun_lain_tidak_dihitung(): void
    {
        $rule = $this->rule('monthly_late_count', '>=', 2);
        $this->log('2026-08-31', late: 10);
        $this->log('2026-10-01', late: 10);
        $this->log('2025-09-02', late: 10);
        $this->log('2026-09-02', late: 10);

        $this->evaluate();

        $this->assertCount(0, $this->violations($rule));
    }

    public function test_bulan_yang_belum_dimulai_tidak_dievaluasi(): void
    {
        $this->rule('monthly_late_count', '>=', 1);
        $this->log('2026-09-01', late: 10);

        $result = $this->evaluator->evaluateEmployee($this->employee, Carbon::create(2026, 11, 1));

        $this->assertSame(['created' => 0, 'updated' => 0, 'removed' => 0], $result);
    }

    public function test_bulan_berjalan_hanya_memakai_data_sampai_hari_ini(): void
    {
        $this->travelTo(Carbon::create(2026, 9, 10, 9, 0));
        $rule = $this->rule('consecutive_absence', '>=', 3);
        $this->log('2026-09-08', 'absent');
        $this->log('2026-09-09', 'absent');
        $this->log('2026-09-10', 'absent');

        $this->evaluate();

        $this->assertSame(3.0, (float) $this->violations($rule)->sole()->trigger_value);
    }

    public function test_aturan_yang_dinonaktifkan_membersihkan_pelanggarannya_dan_bisa_kembali_saat_diaktifkan(): void
    {
        $rule = $this->rule('monthly_late_count', '>=', 1);
        $this->log('2026-09-01', late: 10);
        $this->evaluate();
        $this->assertCount(1, $this->violations($rule));

        $rule->update(['is_active' => false]);
        $result = $this->evaluate();
        $this->assertSame([1, 0], [$result['removed'], $this->violations($rule)->count()]);

        $rule->update(['is_active' => true]);
        $this->evaluate();
        $this->assertCount(1, $this->violations($rule));
    }

    public function test_tanpa_aturan_aktif_pelanggaran_sisa_tetap_dibersihkan_oleh_evaluasi_bulanan(): void
    {
        $rule = $this->rule('monthly_late_count', '>=', 1);
        $this->log('2026-09-01', late: 10);
        $this->evaluate();
        $rule->update(['is_active' => false]);   // tidak ada lagi aturan aktif sama sekali

        $this->evaluator->evaluateMonth($this->september);

        $this->assertCount(0, $this->violations($rule));
    }

    public function test_aturan_nonaktif_tidak_memotong_gaji_walau_pelanggaran_masih_tersimpan(): void
    {
        [$rule, $period] = $this->payrollWithLateRule();
        $this->log('2026-09-01', late: 20);
        app(PayrollGenerationService::class)->generate($period);

        // Pelanggaran dipertahankan karena sudah menjadi dasar surat peringatan, lalu aturannya dinonaktifkan
        $violation = $this->violations($rule)->sole();
        $templateId = DB::table('letter_templates')->insertGetId(['template_code' => 'T'.uniqid(), 'template_name' => 'SP', 'letter_category' => 'generated', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('employee_warning_letters')->insert([
            'employee_id' => $this->employee->id, 'letter_template_id' => $templateId, 'letter_number' => 'SP-'.uniqid(),
            'telegram_file_id' => 'x', 'issued_at' => '2026-09-30', 'attendance_rule_violation_id' => $violation->id,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $rule->update(['is_active' => false]);
        app(PayrollGenerationService::class)->generate($period);

        $this->assertSame(1, AttendanceRuleViolation::where('id', $violation->id)->count(), 'Pelanggaran bersurat tetap sebagai riwayat.');
        $payroll = EmployeePayroll::where('payroll_period_id', $period->id)->where('employee_id', $this->employee->id)->firstOrFail();
        $this->assertSame(0, $payroll->items()->where('component_code_snapshot', 'LATE_DEDUCTION')->count());
    }

    public function test_aturan_kerja_dan_sesi_tidak_saling_memakai_data(): void
    {
        $sessionRule = $this->rule('monthly_late_count', '>=', 1, type: 'session');
        $workRule = $this->rule('monthly_late_count', '>=', 1, type: 'work_schedule');
        $this->log('2026-09-01', late: 10);   // hanya data kerja

        $this->evaluate();

        $this->assertSame([0, 1], [$this->violations($sessionRule)->count(), $this->violations($workRule)->count()]);
    }

    public function test_pegawai_tanpa_rekap_dilewati_dan_pegawai_nonaktif_tidak_dievaluasi_bulanan(): void
    {
        $rule = $this->rule('monthly_late_count', '>=', 1);
        $noRecap = $this->makeEmployee();   // tanpa log, tanpa rekap
        $inactive = $this->makeEmployee();
        $this->log('2026-09-01', late: 10, employee: $inactive);
        $inactive->update(['is_active' => false]);

        $this->assertSame(['created' => 0, 'updated' => 0, 'removed' => 0], $this->evaluate($noRecap));

        $this->evaluator->evaluateMonth($this->september);
        $this->assertSame(0, AttendanceRuleViolation::where('employee_id', $inactive->id)->count());
        $this->assertSame(0, AttendanceRuleViolation::where('employee_id', $noRecap->id)->count());
    }

    public function test_evaluate_month_memproses_semua_pegawai_aktif(): void
    {
        $rule = $this->rule('monthly_late_count', '>=', 1);
        $other = $this->makeEmployee();
        $this->log('2026-09-01', late: 10);
        $this->log('2026-09-01', late: 10, employee: $other);

        $result = $this->evaluator->evaluateMonth($this->september);

        $this->assertSame(2, $result['created']);
        $this->assertSame(2, AttendanceRuleViolation::where('attendance_rule_id', $rule->id)->count());
    }

    // ============================ idempoten & pembersihan ============================

    public function test_dijalankan_ulang_tidak_menggandakan_pelanggaran_atau_eksekusi(): void
    {
        $rule = $this->rule('monthly_late_count', '>=', 1, actions: ['notification', 'warning_letter']);
        $this->log('2026-09-01', late: 10);

        $this->evaluate();
        $second = $this->evaluate();
        $this->evaluate();

        $this->assertSame(['created' => 0, 'updated' => 0, 'removed' => 0], $second);
        $this->assertSame(1, $this->violations($rule)->count());
        $this->assertSame(2, AttendanceRuleActionExecution::where('attendance_rule_violation_id', $this->violations($rule)->first()->id)->count());
    }

    public function test_nilai_pelanggaran_diperbarui_saat_absensi_berubah(): void
    {
        $rule = $this->rule('monthly_late_minutes', '>=', 10);
        $this->log('2026-09-01', late: 15);
        $this->evaluate();
        $this->log('2026-09-02', late: 20);

        $result = $this->evaluate();

        $this->assertSame(1, $result['updated']);
        $this->assertSame(35.0, (float) $this->violations($rule)->sole()->trigger_value);
    }

    public function test_pelanggaran_yang_tidak_lagi_terpenuhi_dihapus_beserta_eksekusinya(): void
    {
        $rule = $this->rule('monthly_late_count', '>=', 1, actions: ['notification']);
        $this->log('2026-09-01', late: 10);
        $this->evaluate();
        $violationId = $this->violations($rule)->sole()->id;

        EmployeeWorkAttendanceLog::query()->where('employee_id', $this->employee->id)->update(['late_minutes' => 0]);
        $this->recomputeRecap();
        $result = $this->evaluate();

        $this->assertSame(1, $result['removed']);
        $this->assertSame([0, 0], [AttendanceRuleViolation::where('id', $violationId)->count(), AttendanceRuleActionExecution::where('attendance_rule_violation_id', $violationId)->count()]);
    }

    public function test_pelanggaran_yang_sudah_dirujuk_surat_peringatan_dipertahankan(): void
    {
        $rule = $this->rule('monthly_late_count', '>=', 1);
        $this->log('2026-09-01', late: 10);
        $this->evaluate();
        $violation = $this->violations($rule)->sole();
        $templateId = DB::table('letter_templates')->insertGetId(['template_code' => 'T'.uniqid(), 'template_name' => 'SP', 'letter_category' => 'generated', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('employee_warning_letters')->insert([
            'employee_id' => $this->employee->id, 'letter_template_id' => $templateId, 'letter_number' => 'SP-'.uniqid(),
            'telegram_file_id' => 'x', 'issued_at' => '2026-09-30', 'attendance_rule_violation_id' => $violation->id,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        EmployeeWorkAttendanceLog::query()->where('employee_id', $this->employee->id)->update(['late_minutes' => 0]);
        $this->recomputeRecap();
        $result = $this->evaluate();

        $this->assertSame(0, $result['removed']);
        $this->assertSame(1, AttendanceRuleViolation::where('id', $violation->id)->count());
    }

    public function test_aksi_baru_pada_aturan_mendapat_eksekusi_pada_evaluasi_berikutnya_dan_yang_diproses_tidak_diubah(): void
    {
        $rule = $this->rule('monthly_late_count', '>=', 1, actions: ['notification']);
        $this->log('2026-09-01', late: 10);
        $this->evaluate();
        $violation = $this->violations($rule)->sole();
        $violation->actionExecutions()->update(['status' => 'processed', 'executed_at' => now()]);

        AttendanceRuleAction::create(['attendance_rule_id' => $rule->id, 'action_type' => 'warning_letter', 'action_order' => 2]);
        $this->evaluate();

        $this->assertEqualsCanonicalizing(['processed', 'pending'], $violation->actionExecutions()->pluck('status')->all());
    }

    public function test_libur_yang_ditetapkan_belakangan_menghapus_alpha_beruntun_yang_bergantung_padanya(): void
    {
        $rule = $this->rule('monthly_absence', '>=', 2);
        $this->log('2026-09-01', 'absent');
        $this->log('2026-09-02', 'absent');
        $this->evaluate();
        $this->assertCount(1, $this->violations($rule));

        Holiday::create(['holiday_name' => 'Libur', 'holiday_start_date' => '2026-09-01', 'holiday_end_date' => '2026-09-02']);   // observer menyegarkan rekap
        $this->evaluate();

        $this->assertCount(0, $this->violations($rule));
    }

    // ============================ sesi ============================

    private function tutorSession(string $date, bool $attended, int $late = 0): void
    {
        $classRoom = ClassRoom::factory()->create(['program_id' => Program::factory()->create()->id, 'branch_id' => $this->branch->id, 'tutor_id' => $this->employee->id]);
        $schedule = ClassSchedule::create(['class_id' => $classRoom->id, 'schedule_date' => $date, 'start_time' => '11:00', 'end_time' => '13:00', 'late_tolerance_minutes' => 15]);
        $session = SessionSchedule::create(['class_schedule_id' => $schedule->id, 'employee_id' => $this->employee->id]);

        if ($attended) {
            EmployeeSessionAttendanceLog::create(['session_schedule_id' => $session->id, 'employee_id' => $this->employee->id, 'status' => 'present', 'late_minutes' => $late]);
        }
    }

    public function test_alpha_sesi_bulanan_dihitung_per_hari(): void
    {
        $rule = $this->rule('monthly_absence', '>=', 2, type: 'session');
        $this->tutorSession('2026-09-02', false);
        $this->tutorSession('2026-09-02', false);   // hari yang sama: tetap 1 hari
        $this->tutorSession('2026-09-03', false);
        $this->tutorSession('2026-09-04', true);

        $this->evaluate();

        $this->assertSame(2.0, (float) $this->violations($rule)->sole()->trigger_value);
    }

    public function test_alpha_sesi_beruntun(): void
    {
        $rule = $this->rule('consecutive_absence', '>=', 3, type: 'session');
        foreach (['2026-09-02', '2026-09-03', '2026-09-04'] as $date) {
            $this->tutorSession($date, false);
        }
        $this->tutorSession('2026-09-05', true);
        $this->tutorSession('2026-09-07', false);

        $this->evaluate();

        $this->assertSame(3.0, (float) $this->violations($rule)->sole()->trigger_value);
    }

    public function test_terlambat_sesi_bulanan_dan_harian(): void
    {
        $count = $this->rule('monthly_late_count', '>=', 2, type: 'session');
        $minutes = $this->rule('monthly_late_minutes', '>=', 40, type: 'session');
        $daily = $this->rule('daily_late', '>=', 20, type: 'session');
        $this->tutorSession('2026-09-02', true, late: 25);
        $this->tutorSession('2026-09-03', true, late: 15);
        $this->tutorSession('2026-09-04', true, late: 0);

        $this->evaluate();

        $this->assertSame([2.0, 40.0], [(float) $this->violations($count)->sole()->trigger_value, (float) $this->violations($minutes)->sole()->trigger_value]);
        $this->assertSame(['2026-09-02'], $this->violations($daily)->map(fn ($v) => $v->period_start_date->toDateString())->all());
    }

    public function test_pegawai_tanpa_sesi_tidak_kena_aturan_sesi(): void
    {
        $rule = $this->rule('monthly_absence', '>=', 1, type: 'session');

        $this->evaluate();

        $this->assertCount(0, $this->violations($rule));
    }

    // ============================ command & jadwal ============================

    public function test_command_dengan_bulan_tertentu_dan_format_salah(): void
    {
        $this->rule('monthly_late_count', '>=', 1);
        $this->log('2026-09-01', late: 10);

        $this->artisan('attendance:evaluate-rules', ['--month' => '2026-09'])->expectsOutputToContain('2026-09: 1 baru')->assertSuccessful();
        $this->artisan('attendance:evaluate-rules', ['--month' => '09/2026'])->assertFailed();
    }

    public function test_command_bawaan_mengevaluasi_bulan_lalu_hanya_di_lima_hari_pertama(): void
    {
        $this->rule('monthly_late_count', '>=', 1);
        $this->log('2026-09-01', late: 10);

        $this->travelTo(Carbon::create(2026, 10, 3, 9, 0));
        $this->artisan('attendance:evaluate-rules')->expectsOutputToContain('2026-09')->expectsOutputToContain('2026-10')->assertSuccessful();

        $this->travelTo(Carbon::create(2026, 10, 20, 9, 0));
        $this->artisan('attendance:evaluate-rules')->expectsOutputToContain('2026-10')->assertSuccessful();
    }

    public function test_command_terdaftar_di_scheduler(): void
    {
        $this->artisan('schedule:list')->expectsOutputToContain('attendance:evaluate-rules')->assertSuccessful();
    }

    // ============================ payroll ============================

    private function payrollWithLateRule(float $perMinute = 1000, int $triggerMinutes = 5): array
    {
        $base = PayrollComponent::firstOrCreate(['component_code' => 'BASE_SALARY'], ['component_name' => 'Gaji Pokok', 'component_type' => 'earning', 'calculation_method' => 'fixed', 'is_active' => true]);
        $deduction = PayrollComponent::firstOrCreate(['component_code' => 'LATE_DEDUCTION'], ['component_name' => 'Potongan Terlambat', 'component_type' => 'deduction', 'calculation_method' => 'manual', 'is_active' => true]);
        EmployeeCompensation::create(['employee_id' => $this->employee->id, 'payroll_component_id' => $base->id, 'value' => 5_000_000]);

        $rule = $this->rule('monthly_late_minutes', '>=', $triggerMinutes, actions: ['payroll_deduction']);
        AttendanceRulePayrollAction::create([
            'attendance_rule_action_id' => $rule->actions->first()->id, 'payroll_component_id' => $deduction->id,
            'deduction_type' => 'per_minute', 'deduction_value' => $perMinute,
        ]);

        return [$rule, PayrollPeriod::create(['period_year' => 2026, 'period_month' => 9, 'status' => 'finalized', 'confirmed_at' => now()])];
    }

    private function recomputeRecap(): void
    {
        app(AttendanceRecapRepository::class)->upsertForEmployee($this->employee->id, $this->september->copy());
    }

    public function test_payroll_mengevaluasi_aturan_sendiri_tanpa_job_harian_dan_memotong_per_menit(): void
    {
        [$rule, $period] = $this->payrollWithLateRule();
        $this->log('2026-09-01', late: 20);
        $this->log('2026-09-02', late: 15);
        $this->assertCount(0, $this->violations($rule), 'Job harian belum pernah berjalan.');

        app(PayrollGenerationService::class)->generate($period);

        $payroll = EmployeePayroll::where('payroll_period_id', $period->id)->where('employee_id', $this->employee->id)->firstOrFail();
        $item = $payroll->items()->where('component_code_snapshot', 'LATE_DEDUCTION')->firstOrFail();
        $this->assertEquals([35_000.0, 'attendance'], [(float) $item->total_amount, $item->source_type]);
        $this->assertSame('processed', $this->violations($rule)->sole()->actionExecutions()->value('status'));
    }

    public function test_generate_ulang_payroll_tidak_menggandakan_potongan_terlambat(): void
    {
        [$rule, $period] = $this->payrollWithLateRule();
        $this->log('2026-09-01', late: 20);

        app(PayrollGenerationService::class)->generate($period);
        app(PayrollGenerationService::class)->generate($period);

        $payroll = EmployeePayroll::where('payroll_period_id', $period->id)->where('employee_id', $this->employee->id)->firstOrFail();
        $this->assertSame(1, $payroll->items()->where('component_code_snapshot', 'LATE_DEDUCTION')->count());
        $this->assertSame(1, $this->violations($rule)->count());
    }

    public function test_koreksi_absensi_sebelum_generate_ulang_menghilangkan_potongan_basi(): void
    {
        [$rule, $period] = $this->payrollWithLateRule();
        $this->log('2026-09-01', late: 20);
        app(PayrollGenerationService::class)->generate($period);

        EmployeeWorkAttendanceLog::query()->where('employee_id', $this->employee->id)->update(['late_minutes' => 0]);
        $this->recomputeRecap();
        app(PayrollGenerationService::class)->generate($period);

        $payroll = EmployeePayroll::where('payroll_period_id', $period->id)->where('employee_id', $this->employee->id)->firstOrFail();
        $this->assertSame(0, $payroll->items()->where('component_code_snapshot', 'LATE_DEDUCTION')->count());
        $this->assertCount(0, $this->violations($rule));
    }

    public function test_di_bawah_ambang_tidak_ada_potongan_terlambat(): void
    {
        [, $period] = $this->payrollWithLateRule(triggerMinutes: 60);
        $this->log('2026-09-01', late: 20);

        app(PayrollGenerationService::class)->generate($period);

        $payroll = EmployeePayroll::where('payroll_period_id', $period->id)->where('employee_id', $this->employee->id)->firstOrFail();
        $this->assertSame(0, $payroll->items()->where('component_code_snapshot', 'LATE_DEDUCTION')->count());
    }
}
