<?php

namespace Tests\Feature\Payroll;

use App\Models\Employee;
use App\Models\EmployeeCompensation;
use App\Models\EmployeeKpiEvaluation;
use App\Models\EmployeePayroll;
use App\Models\EmployeeWorkAttendanceLog;
use App\Models\EmploymentStatus;
use App\Models\Holiday;
use App\Models\KpiBonusRule;
use App\Models\KpiBonusRuleTier;
use App\Models\KpiTemplate;
use App\Models\MarketingBonusRule;
use App\Models\MarketingBonusRuleTier;
use App\Models\PayrollComponent;
use App\Models\PayrollItem;
use App\Models\PayrollPeriod;
use App\Models\Position;
use App\Models\SpecialBonusRule;
use App\Models\SpecialBonusRuleCondition;
use App\Models\User;
use App\Models\WorkScheduleAssignment;
use App\Models\WorkScheduleDay;
use App\Models\WorkScheduleRule;
use App\Services\Payroll\PayrollGenerationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

// Bonus yang bergantung pada kehadiran dan KPI, dari absensi nyata sampai item payroll (September 2026):
//   bonus khusus "hari hadir >= N" (dari rekap), bonus KPI (dari evaluasi final bulan itu), bonus marketing (masa kerja).
class AttendanceBonusPayrollTest extends TestCase
{
    private Employee $employee;

    private PayrollPeriod $period;

    protected function setUp(): void
    {
        parent::setUp();

        $branchId = DB::table('branches')->value('id');
        $this->employee = Employee::factory()->create(['branch_id' => $branchId, 'is_active' => true, 'join_date' => '2024-01-01']);
        $position = Position::create(['position_name' => 'Staff'.uniqid(), 'hierarchy_order' => 5]);
        EmploymentStatus::create(['employees_id' => $this->employee->id, 'position_id' => $position->id, 'type_employment' => 'permanent']);

        $rule = WorkScheduleRule::factory()->create(['is_required' => true, 'is_active' => true]);
        foreach ([1, 2, 3, 4, 5, 6] as $day) {
            WorkScheduleDay::create(['work_schedule_rule_id' => $rule->id, 'day_of_week' => $day]);
        }
        WorkScheduleAssignment::create([
            'work_schedule_rule_id' => $rule->id, 'assignable_type' => Employee::class, 'assignable_id' => $this->employee->id,
            'effective_start_date' => '2026-01-01', 'is_active' => true,
        ]);

        $salary = PayrollComponent::firstOrCreate(['component_code' => 'BASE_SALARY'], ['component_name' => 'Gaji Pokok', 'component_type' => 'earning', 'calculation_method' => 'fixed', 'is_active' => true]);
        foreach (['MARKETING', 'KPI', 'SPECIAL'] as $type) {
            PayrollComponent::firstOrCreate(['component_code' => 'BONUS_'.$type], ['component_name' => 'Bonus '.$type, 'component_type' => 'earning', 'calculation_method' => 'manual', 'is_active' => true]);
        }
        EmployeeCompensation::create(['employee_id' => $this->employee->id, 'payroll_component_id' => $salary->id, 'value' => 5_000_000]);

        // Perubahan bonus rule diaudit dan mensyaratkan user dengan employee record
        $this->actingAs(User::factory()->create(['employee_id' => Employee::factory()->create(['is_active' => false])->id]));

        $this->period = PayrollPeriod::create(['period_year' => 2026, 'period_month' => 9, 'status' => 'finalized', 'confirmed_at' => now()]);
    }

    private function present(string $date, bool $missedCheckOut = false): void
    {
        EmployeeWorkAttendanceLog::factory()->present()->create([
            'employee_id' => $this->employee->id, 'branch_id' => $this->employee->branch_id,
            'attendance_date' => $date, 'is_check_out_missed' => $missedCheckOut,
        ]);
    }

    private function attendanceBonus(int $minimumDays, float $reward = 300_000): SpecialBonusRule
    {
        $rule = SpecialBonusRule::factory()->create([
            'scope_type' => 'employee', 'employee_id' => $this->employee->id, 'reward_type' => 'fixed', 'reward_value' => $reward,
            'reward_basis' => 'base_salary', 'condition_mode' => 'all', 'is_active' => true,
        ]);
        SpecialBonusRuleCondition::create([
            'special_bonus_rule_id' => $rule->id, 'metric_code' => 'attendance_days_present', 'period_type' => 'payroll_period',
            'operator' => '>=', 'target_value' => $minimumDays, 'data_source' => 'attendance_days_present', 'is_active' => true,
        ]);

        return $rule;
    }

    private function evaluation(string $start, string $end, float $score, string $status = 'finalized', string $type = 'monthly'): void
    {
        $template = KpiTemplate::firstOrCreate(['template_code' => 'TPL-BONUS'], ['template_name' => 'Template Bonus', 'is_active' => true, 'created_by_employee_id' => $this->employee->id]);
        EmployeeKpiEvaluation::create([
            'employee_id' => $this->employee->id, 'kpi_template_id' => $template->id, 'evaluator_employee_id' => $this->employee->id,
            'employee_name_snapshot' => 'X', 'period_type' => $type, 'period_start_date' => $start, 'period_end_date' => $end,
            'status' => $status, 'total_score' => $score,
        ]);
    }

    private function kpiBonus(array $tiers): KpiBonusRule
    {
        $rule = KpiBonusRule::factory()->create(['scope_type' => 'employee', 'employee_id' => $this->employee->id, 'reward_basis' => 'base_salary']);
        foreach ($tiers as [$min, $max, $reward]) {
            KpiBonusRuleTier::create(['kpi_bonus_rule_id' => $rule->id, 'minimum_score' => $min, 'maximum_score' => $max, 'reward_type' => 'fixed', 'reward_value' => $reward]);
        }

        return $rule;
    }

    private function payroll(): EmployeePayroll
    {
        app(PayrollGenerationService::class)->generate($this->period);

        return EmployeePayroll::where('payroll_period_id', $this->period->id)->where('employee_id', $this->employee->id)->firstOrFail();
    }

    private function bonusTotal(EmployeePayroll $payroll, string $type): float
    {
        return (float) PayrollItem::where('employee_payroll_id', $payroll->id)->where('source_type', 'bonus')
            ->where('component_code_snapshot', 'BONUS_'.strtoupper($type))->sum('total_amount');
    }

    // ---- bonus kehadiran (kondisi hari hadir) -----------------------------------------------------------------

    public function test_bonus_kehadiran_diberikan_bila_hari_hadir_dari_absensi_mencapai_target(): void
    {
        foreach (['2026-09-01', '2026-09-02', '2026-09-03', '2026-09-04'] as $date) {
            $this->present($date);
        }
        $this->attendanceBonus(4);

        $payroll = $this->payroll();

        $this->assertEquals(300_000.0, $this->bonusTotal($payroll, 'special'));
        $this->assertEquals(5_300_000.0, (float) $payroll->net_amount);
    }

    public function test_bonus_kehadiran_tidak_diberikan_bila_kurang_satu_hari(): void
    {
        foreach (['2026-09-01', '2026-09-02', '2026-09-03'] as $date) {
            $this->present($date);
        }
        $this->attendanceBonus(4);

        $this->assertEquals(0.0, $this->bonusTotal($this->payroll(), 'special'));
    }

    public function test_hari_lupa_absen_keluar_tetap_dihitung_hadir_untuk_bonus(): void
    {
        foreach (['2026-09-01', '2026-09-02', '2026-09-03'] as $date) {
            $this->present($date);
        }
        $this->present('2026-09-04', missedCheckOut: true);
        $this->attendanceBonus(4);

        $this->assertEquals(300_000.0, $this->bonusTotal($this->payroll(), 'special'));
    }

    public function test_kehadiran_bulan_dan_tahun_lain_tidak_menolong_bonus(): void
    {
        foreach (['2026-08-31', '2026-10-01', '2025-09-01', '2025-09-02'] as $date) {
            $this->present($date);
        }
        $this->present('2026-09-01');
        $this->attendanceBonus(2);

        $this->assertEquals(0.0, $this->bonusTotal($this->payroll(), 'special'));
    }

    public function test_sakit_dan_cuti_tidak_dihitung_hari_hadir_untuk_bonus(): void
    {
        $this->present('2026-09-01');
        foreach (['2026-09-02' => 'sick', '2026-09-03' => 'leave', '2026-09-04' => 'permission'] as $date => $status) {
            EmployeeWorkAttendanceLog::factory()->create([
                'employee_id' => $this->employee->id, 'branch_id' => $this->employee->branch_id, 'attendance_date' => $date, 'status' => $status,
            ]);
        }
        $this->attendanceBonus(2);

        $this->assertEquals(0.0, $this->bonusTotal($this->payroll(), 'special'));
    }

    public function test_libur_yang_ditambah_belakangan_tidak_mengurangi_hari_hadir_untuk_bonus(): void
    {
        foreach (['2026-09-01', '2026-09-02', '2026-09-03'] as $date) {
            $this->present($date);
        }
        $this->attendanceBonus(3);

        Holiday::create(['holiday_name' => 'Libur', 'holiday_start_date' => '2026-09-03', 'holiday_end_date' => '2026-09-03']);

        $this->assertEquals(300_000.0, $this->bonusTotal($this->payroll(), 'special'), 'Hadir di hari yang belakangan libur tetap hadir.');
    }

    public function test_pegawai_wajib_absen_tanpa_rekap_dilewati_dari_payroll_tanpa_bonus(): void
    {
        $this->present('2026-09-01');
        $this->attendanceBonus(1);
        DB::table('employee_attendance_monthly_recaps')->where('employee_id', $this->employee->id)->delete();

        // Tanpa rekap pegawai wajib absen dilewati dari payroll (GP-06); tidak ada bonus yang bocor
        app(PayrollGenerationService::class)->generate($this->period);
        $this->assertDatabaseMissing('employee_payrolls', ['payroll_period_id' => $this->period->id, 'employee_id' => $this->employee->id]);
    }

    // ---- bonus KPI ---------------------------------------------------------------------------------------------

    public function test_bonus_kpi_memakai_evaluasi_final_yang_berakhir_di_bulan_payroll(): void
    {
        $this->present('2026-09-01');
        $this->kpiBonus([[0, 59.99, 100_000], [60, 79.99, 400_000], [80, null, 900_000]]);
        $this->evaluation('2026-09-01', '2026-09-30', 85);

        $this->assertEquals(900_000.0, $this->bonusTotal($this->payroll(), 'kpi'));
    }

    public function test_bonus_kpi_tidak_memakai_skor_bulan_lalu(): void
    {
        $this->present('2026-09-01');
        $this->kpiBonus([[0, 59.99, 100_000], [60, null, 400_000]]);
        $this->evaluation('2026-08-01', '2026-08-31', 90);   // hanya ada evaluasi Agustus

        $this->assertEquals(0.0, $this->bonusTotal($this->payroll(), 'kpi'), 'Skor Agustus tidak boleh dipakai untuk payroll September.');
    }

    public function test_bonus_kpi_tidak_memakai_skor_tahun_lalu(): void
    {
        $this->present('2026-09-01');
        $this->kpiBonus([[0, 59.99, 100_000], [60, null, 400_000]]);
        $this->evaluation('2025-09-01', '2025-09-30', 95);

        $this->assertEquals(0.0, $this->bonusTotal($this->payroll(), 'kpi'));
    }

    public function test_tanpa_evaluasi_tier_skor_nol_tidak_dibayar(): void
    {
        $this->present('2026-09-01');
        $this->kpiBonus([[0, 49.99, 150_000], [50, null, 500_000]]);   // tier terendah mulai dari 0

        $this->assertEquals(0.0, $this->bonusTotal($this->payroll(), 'kpi'), 'Tanpa evaluasi, jangan jatuh ke tier skor 0.');
    }

    public function test_evaluasi_draft_tidak_dipakai(): void
    {
        $this->present('2026-09-01');
        $this->kpiBonus([[60, null, 400_000]]);
        $this->evaluation('2026-09-01', '2026-09-30', 95, status: 'draft');

        $this->assertEquals(0.0, $this->bonusTotal($this->payroll(), 'kpi'));
    }

    public function test_evaluasi_kuartalan_yang_berakhir_di_bulan_payroll_dipakai(): void
    {
        $this->present('2026-09-01');
        $this->kpiBonus([[60, null, 400_000]]);
        $this->evaluation('2026-07-01', '2026-09-30', 70, type: 'quarterly');

        $this->assertEquals(400_000.0, $this->bonusTotal($this->payroll(), 'kpi'));
    }

    public function test_dua_evaluasi_berakhir_di_bulan_yang_sama_memakai_yang_terakhir(): void
    {
        $this->present('2026-09-01');
        $this->kpiBonus([[0, 59.99, 100_000], [60, null, 400_000]]);
        $this->evaluation('2026-09-01', '2026-09-30', 40);
        $this->evaluation('2026-07-01', '2026-09-30', 90, type: 'quarterly');   // id lebih besar, berakhir sama

        $this->assertEquals(400_000.0, $this->bonusTotal($this->payroll(), 'kpi'));
    }

    public function test_batas_tier_skor_inklusif(): void
    {
        $this->present('2026-09-01');
        $this->kpiBonus([[0, 59.99, 100_000], [60, null, 400_000]]);
        $this->evaluation('2026-09-01', '2026-09-30', 60);

        $this->assertEquals(400_000.0, $this->bonusTotal($this->payroll(), 'kpi'));
    }

    // ---- bonus marketing: masa kerja dihitung terhadap periode payroll ------------------------------------------

    public function test_masa_kerja_dihitung_dari_tanggal_bergabung_sampai_akhir_periode_bukan_sampai_hari_ini(): void
    {
        $this->present('2026-09-01');
        $this->employee->update(['join_date' => '2025-01-15']);   // 20 bulan penuh sampai 30 Sep 2026
        DB::table('marketing_performances')->insert([
            'employee_id' => $this->employee->id, 'period_year' => 2026, 'period_month' => 9, 'achievement_percentage' => 100,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $rule = MarketingBonusRule::factory()->create(['scope_type' => 'employee', 'employee_id' => $this->employee->id, 'bonus_basis' => 'achievement', 'reward_basis' => 'base_salary']);
        MarketingBonusRuleTier::create([
            'marketing_bonus_rule_id' => $rule->id, 'minimum_tenure_months' => 12, 'maximum_tenure_months' => 24,
            'minimum_achievement_percentage' => 90, 'maximum_achievement_percentage' => null, 'reward_type' => 'fixed', 'reward_value' => 750_000,
        ]);

        $this->assertEquals(750_000.0, $this->bonusTotal($this->payroll(), 'marketing'));

        // Payroll dibuat ulang bertahun-tahun kemudian: hasil periode September 2026 tidak boleh berubah
        $this->travelTo(Carbon::create(2030, 1, 1));
        $this->assertEquals(750_000.0, $this->bonusTotal($this->payroll(), 'marketing'));
    }

    public function test_pegawai_yang_baru_bergabung_setelah_periode_masa_kerjanya_nol(): void
    {
        $this->present('2026-09-01');
        $this->employee->update(['join_date' => '2026-10-15']);
        DB::table('marketing_performances')->insert([
            'employee_id' => $this->employee->id, 'period_year' => 2026, 'period_month' => 9, 'achievement_percentage' => 100,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $rule = MarketingBonusRule::factory()->create(['scope_type' => 'employee', 'employee_id' => $this->employee->id, 'bonus_basis' => 'achievement', 'reward_basis' => 'base_salary']);
        MarketingBonusRuleTier::create([
            'marketing_bonus_rule_id' => $rule->id, 'minimum_tenure_months' => 1, 'maximum_tenure_months' => null,
            'minimum_achievement_percentage' => 0, 'maximum_achievement_percentage' => null, 'reward_type' => 'fixed', 'reward_value' => 100_000,
        ]);

        $this->assertEquals(0.0, $this->bonusTotal($this->payroll(), 'marketing'));
    }

    // ---- idempotensi ---------------------------------------------------------------------------------------------

    public function test_generate_ulang_tidak_menggandakan_bonus(): void
    {
        foreach (['2026-09-01', '2026-09-02'] as $date) {
            $this->present($date);
        }
        $this->attendanceBonus(2);
        $this->kpiBonus([[0, null, 200_000]]);
        $this->evaluation('2026-09-01', '2026-09-30', 70);

        $this->payroll();
        $payroll = $this->payroll();

        $this->assertEquals([300_000.0, 200_000.0], [$this->bonusTotal($payroll, 'special'), $this->bonusTotal($payroll, 'kpi')]);
        $this->assertEquals(5_500_000.0, (float) $payroll->net_amount);
    }
}
