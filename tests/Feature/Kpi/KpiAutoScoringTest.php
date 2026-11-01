<?php

namespace Tests\Feature\Kpi;

use App\Models\Employee;
use App\Models\EmployeeAttendanceMonthlyRecap;
use App\Models\EmployeeKpiEvaluation;
use App\Models\EmployeeKpiEvaluationItem;
use App\Models\EmployeeWorkAttendanceLog;
use App\Models\KpiTemplate;
use App\Models\KpiTemplateIndicator;
use App\Services\Kpi\KpiDataSourceService;
use App\Services\Kpi\KpiEvaluationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

// Skor indikator otomatis harus langsung terhitung saat evaluasi dibuat, dan tidak boleh tercemar
// data bulan/tahun lain, nilai di luar batas, atau template yang bobotnya tidak 100.
class KpiAutoScoringTest extends TestCase
{
    private Employee $evaluator;

    private Employee $employee;

    private KpiEvaluationService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->evaluator = Employee::factory()->create();
        $this->employee = Employee::factory()->create();
        $this->service = app(KpiEvaluationService::class);
    }

    /** @param list<array{source: ?string, weight: float, target: ?float, active?: bool}> $indicators */
    private function template(array $indicators): KpiTemplate
    {
        $template = KpiTemplate::create([
            'template_code' => 'TPL-'.uniqid(),
            'template_name' => 'Template Auto',
            'is_active' => true,
            'created_by_employee_id' => $this->evaluator->id,
        ]);

        foreach ($indicators as $i => $ind) {
            KpiTemplateIndicator::create([
                'kpi_template_id' => $template->id,
                'indicator_code' => 'IND-'.($i + 1),
                'indicator_name' => 'Indikator '.($i + 1),
                'target_value' => $ind['target'],
                'weight' => $ind['weight'],
                'sequence_number' => $i + 1,
                'data_source_type' => $ind['source'],
                'is_active' => $ind['active'] ?? true,
            ]);
        }

        return $template;
    }

    private function evaluate(KpiTemplate $template, string $type = 'monthly', string $start = '2026-09-01', string $end = '2026-09-30'): EmployeeKpiEvaluation
    {
        return $this->service->create([
            'employee_id' => $this->employee->id,
            'kpi_template_id' => $template->id,
            'period_type' => $type,
            'period_start_date' => $start,
            'period_end_date' => $end,
        ], $this->evaluator);
    }

    private function resolve(string $source, string $start = '2026-09-01', string $end = '2026-09-30'): ?array
    {
        return app(KpiDataSourceService::class)->resolve($source, $this->employee, Carbon::parse($start), Carbon::parse($end));
    }

    private function logPresent(string $date, bool $missed = false): void
    {
        EmployeeWorkAttendanceLog::factory()->present()->create([
            'employee_id' => $this->employee->id,
            'branch_id' => $this->employee->branch_id,
            'attendance_date' => $date,
            'is_check_out_missed' => $missed,
        ]);
    }

    private function recap(int $year, int $month, int $scheduled, int $present): void
    {
        EmployeeAttendanceMonthlyRecap::updateOrCreate(
            ['employee_id' => $this->employee->id, 'period_year' => $year, 'period_month' => $month],
            ['total_scheduled_working_days' => $scheduled, 'total_present' => $present],
        );
    }

    // ---- skor langsung terhitung ------------------------------------------------------------

    public function test_item_otomatis_langsung_punya_pencapaian_dan_skor_saat_evaluasi_dibuat(): void
    {
        $this->logPresent('2026-09-01');
        $this->logPresent('2026-09-02');
        $this->logPresent('2026-09-03');
        $this->logPresent('2026-09-04');
        $this->logPresent('2026-09-07', missed: true);   // 4 dari 5 hari lengkap = 80%
        $this->recap(2026, 9, scheduled: 20, present: 10); // 50%

        $evaluation = $this->evaluate($this->template([
            ['source' => 'attendance.attendance_rate', 'weight' => 80, 'target' => 100],
            ['source' => 'attendance.check_out_compliance_rate', 'weight' => 20, 'target' => 100],
        ]));

        $items = $evaluation->items()->orderBy('id')->get();

        $this->assertEquals(50.0, (float) $items[0]->actual_value);
        $this->assertEquals(50.0, (float) $items[0]->achievement_percentage);
        $this->assertEquals(40.0, (float) $items[0]->score);
        $this->assertEquals(80.0, (float) $items[1]->actual_value);
        $this->assertEquals(16.0, (float) $items[1]->score);
        $this->assertEquals(56.0, (float) $items->sum('score'));
    }

    public function test_sumber_tanpa_data_menghasilkan_skor_nol_bukan_error_dan_tanpa_nilai_palsu(): void
    {
        $evaluation = $this->evaluate($this->template([
            ['source' => 'attendance.check_out_compliance_rate', 'weight' => 100, 'target' => 100],
        ]));

        $item = $evaluation->items()->first();
        $this->assertNull($item->actual_value);
        $this->assertEquals(0.0, (float) $item->score);
        $this->assertNull($item->source_type);
    }

    // ---- nilai salah --------------------------------------------------------------------------

    public function test_pencapaian_dibatasi_100_persen_dan_target_kosong_menghasilkan_nol(): void
    {
        $this->assertSame(['achievement' => 100.0, 'score' => 20.0], $this->service->calculate(50, 100, 20));
        $this->assertSame(['achievement' => 0.0, 'score' => 0.0], $this->service->calculate(0, 100, 20));
        $this->assertSame(['achievement' => 0.0, 'score' => 0.0], $this->service->calculate(null, 100, 20));
        $this->assertSame(['achievement' => 50.0, 'score' => 10.0], $this->service->calculate(100, 50, 20));
    }

    public function test_tingkat_kehadiran_tidak_pernah_lebih_dari_100(): void
    {
        $this->recap(2026, 9, scheduled: 20, present: 25);

        $this->assertSame(100.0, $this->resolve('attendance.attendance_rate')['value']);
    }

    public function test_jadwal_nol_hari_tidak_menyebabkan_pembagian_nol(): void
    {
        $this->recap(2026, 9, scheduled: 0, present: 0);

        $this->assertNull($this->resolve('attendance.attendance_rate'));
    }

    // ---- bulan salah --------------------------------------------------------------------------

    public function test_log_bulan_sebelum_dan_sesudah_tidak_bocor_ke_periode(): void
    {
        $this->logPresent('2026-08-31', missed: true);
        $this->logPresent('2026-09-15');
        $this->logPresent('2026-10-01', missed: true);

        $this->assertSame(100.0, $this->resolve('attendance.check_out_compliance_rate')['value']);
    }

    public function test_hari_terakhir_periode_ikut_dihitung(): void
    {
        $this->logPresent('2026-09-01');
        $this->logPresent('2026-09-30', missed: true);

        $this->assertSame(50.0, $this->resolve('attendance.check_out_compliance_rate')['value']);
    }

    public function test_rekap_bulan_tetangga_tidak_dipakai_untuk_periode_bulanan(): void
    {
        $this->recap(2026, 8, scheduled: 20, present: 20);
        $this->recap(2026, 9, scheduled: 20, present: 10);
        $this->recap(2026, 10, scheduled: 20, present: 0);

        $this->assertSame(50.0, $this->resolve('attendance.attendance_rate')['value']);
    }

    public function test_periode_kuartalan_menjumlahkan_semua_bulannya(): void
    {
        $this->recap(2026, 7, scheduled: 20, present: 20);
        $this->recap(2026, 8, scheduled: 20, present: 10);
        $this->recap(2026, 9, scheduled: 20, present: 0);

        $this->assertSame(50.0, $this->resolve('attendance.attendance_rate', '2026-07-01', '2026-09-30')['value']);
        $this->assertSame(30.0, $this->resolve('attendance.total_present', '2026-07-01', '2026-09-30')['value']);
    }

    public function test_rekap_bulan_hilang_menghasilkan_null_bukan_nilai_parsial(): void
    {
        $this->recap(2026, 7, scheduled: 20, present: 20);
        $this->recap(2026, 9, scheduled: 20, present: 20); // Agustus tidak ada

        $this->assertNull($this->resolve('attendance.attendance_rate', '2026-07-01', '2026-09-30'));
    }

    public function test_sumber_marketing_hanya_untuk_periode_satu_bulan(): void
    {
        DB::table('marketing_performances')->insert([
            'employee_id' => $this->employee->id,
            'period_year' => 2026,
            'period_month' => 9,
            'classes_actual' => 7,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->assertSame(7.0, $this->resolve('marketing_performance.classes_actual')['value']);
        $this->assertNull($this->resolve('marketing_performance.classes_actual', '2026-07-01', '2026-09-30'));
    }

    // ---- tahun salah --------------------------------------------------------------------------

    public function test_data_bulan_sama_tahun_lain_tidak_bocor(): void
    {
        $this->logPresent('2025-09-10', missed: true);
        $this->logPresent('2026-09-10');
        $this->recap(2025, 9, scheduled: 20, present: 20);
        $this->recap(2026, 9, scheduled: 20, present: 10);

        $this->assertSame(100.0, $this->resolve('attendance.check_out_compliance_rate')['value']);
        $this->assertSame(50.0, $this->resolve('attendance.attendance_rate')['value']);
    }

    public function test_periode_yang_melintasi_tahun_memakai_bulan_yang_benar(): void
    {
        $this->recap(2025, 11, scheduled: 20, present: 20);
        $this->recap(2025, 12, scheduled: 20, present: 20);
        $this->recap(2026, 1, scheduled: 20, present: 0);
        $this->recap(2025, 1, scheduled: 20, present: 20); // Januari tahun lalu, harus diabaikan

        $this->assertSame(66.67, $this->resolve('attendance.attendance_rate', '2025-11-01', '2026-01-31')['value']);
    }

    // ---- bobot harus 100 ----------------------------------------------------------------------

    public function test_evaluasi_ditolak_bila_total_bobot_bukan_100(): void
    {
        $template = $this->template([
            ['source' => null, 'weight' => 60, 'target' => 100],
            ['source' => null, 'weight' => 30, 'target' => 100],
        ]);

        try {
            $this->evaluate($template);
            $this->fail('Seharusnya ditolak karena total bobot 90.');
        } catch (ValidationException $e) {
            $this->assertStringContainsString('harus 100', $e->errors()['kpi_template_id'][0]);
        }

        $this->assertDatabaseCount('employee_kpi_evaluations', 0);
    }

    public function test_indikator_nonaktif_tidak_dihitung_dalam_total_bobot(): void
    {
        $evaluation = $this->evaluate($this->template([
            ['source' => null, 'weight' => 100, 'target' => 100],
            ['source' => null, 'weight' => 50, 'target' => 100, 'active' => false],
        ]));

        $this->assertCount(1, $evaluation->items);
    }

    public function test_evaluasi_ditolak_bila_ada_indikator_tanpa_target(): void
    {
        $template = $this->template([
            ['source' => null, 'weight' => 70, 'target' => 100],
            ['source' => null, 'weight' => 30, 'target' => null],
        ]);

        $this->expectException(ValidationException::class);
        $this->evaluate($template);
    }

    // ---- nilai otomatis tidak bisa ditimpa ------------------------------------------------------

    public function test_nilai_otomatis_tidak_bisa_ditimpa_manual(): void
    {
        $this->logPresent('2026-09-01');
        $evaluation = $this->evaluate($this->template([
            ['source' => 'attendance.check_out_compliance_rate', 'weight' => 100, 'target' => 100],
        ]));
        $item = $evaluation->items()->first();

        try {
            $this->service->updateItem($item, 5.0, null);
            $this->fail('Nilai otomatis seharusnya tidak bisa ditimpa.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('actual_value', $e->errors());
        }

        $this->assertEquals(100.0, (float) $item->fresh()->actual_value);
    }

    public function test_item_manual_dihitung_ulang_dan_dibatasi_100(): void
    {
        $evaluation = $this->evaluate($this->template([
            ['source' => null, 'weight' => 100, 'target' => 50],
        ]));
        $item = $evaluation->items()->first();

        $this->service->updateItem($item, 500.0, null);

        $item->refresh();
        $this->assertEquals(100.0, (float) $item->achievement_percentage);
        $this->assertEquals(100.0, (float) $item->score);
    }

    // ---- finalisasi -----------------------------------------------------------------------------

    public function test_finalisasi_ditolak_bila_ada_item_tanpa_nilai_aktual(): void
    {
        $evaluation = $this->evaluate($this->template([
            ['source' => 'attendance.check_out_compliance_rate', 'weight' => 100, 'target' => 100],
        ]));

        $this->expectException(ValidationException::class);
        $this->service->finalize($evaluation, null);
    }

    public function test_total_skor_tidak_melebihi_100(): void
    {
        $evaluation = $this->evaluate($this->template([
            ['source' => null, 'weight' => 60, 'target' => 10],
            ['source' => null, 'weight' => 40, 'target' => 10],
        ]));

        foreach ($evaluation->items as $item) {
            $this->service->updateItem($item, 9999.0, null);
        }

        $final = $this->service->finalize($evaluation->fresh(), null);

        $this->assertEquals(100.0, (float) $final->total_score);
        $this->assertSame(2, EmployeeKpiEvaluationItem::where('employee_kpi_evaluation_id', $final->id)->count());
    }
}
