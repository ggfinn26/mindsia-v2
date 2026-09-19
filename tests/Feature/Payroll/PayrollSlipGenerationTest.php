<?php

namespace Tests\Feature\Payroll;

use App\Models\EmployeePayroll;
use App\Models\EmployeePayrollSlip;
use App\Models\PayrollComponent;
use App\Models\PayrollItem;
use App\Models\PayrollPeriod;
use App\Services\Letter\ConvertApiService;
use App\Services\Payroll\PayrollSlipService;
use App\Services\TelegramStorageService;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use Tests\TestCase;

class PayrollSlipGenerationTest extends TestCase
{
    private static int $periodSeq = 0;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createSlipTemplates();
    }

    protected function tearDown(): void
    {
        $this->deleteSlipTemplates();
        parent::tearDown();
    }

    // =========================================================
    // PS-01: Happy path — slip created with telegram_file_id
    // =========================================================

    /** PS-01: generate() creates EmployeePayrollSlip with telegram_file_id */
    public function test_ps01_generate_creates_slip_with_telegram_file_id(): void
    {
        $payroll = $this->makePayrollWithItems();

        [$convertApi, $telegram] = $this->mockDependencies();

        $service = app(PayrollSlipService::class);
        $slip = $service->generate($payroll, null, null, PayrollSlipService::TYPE_REGULAR_TUTOR);

        $this->assertInstanceOf(EmployeePayrollSlip::class, $slip);
        $this->assertEquals('fake_telegram_id', $slip->telegram_file_id);
        $this->assertNotNull($slip->generated_at);
        $this->assertEquals($payroll->id, $slip->employee_payroll_id);
    }

    /** PS-02: generate() called twice uses updateOrCreate — no duplicate records */
    public function test_ps02_regenerate_updates_existing_slip(): void
    {
        $payroll = $this->makePayrollWithItems();

        $this->mockDependencies();
        app(PayrollSlipService::class)->generate($payroll, null, null, PayrollSlipService::TYPE_REGULAR_TUTOR);

        $this->mockDependencies('second_id');
        app(PayrollSlipService::class)->generate($payroll, null, null, PayrollSlipService::TYPE_REGULAR_TUTOR);

        $this->assertEquals(1, EmployeePayrollSlip::where('employee_payroll_id', $payroll->id)->count());
        $this->assertEquals('second_id', EmployeePayrollSlip::where('employee_payroll_id', $payroll->id)->value('telegram_file_id'));
    }

    /** PS-03: generate() persists slip record in DB */
    public function test_ps03_slip_persisted_in_database(): void
    {
        $payroll = $this->makePayrollWithItems();

        $this->mockDependencies();
        app(PayrollSlipService::class)->generate($payroll, null, null, PayrollSlipService::TYPE_REGULAR_TUTOR);

        $this->assertDatabaseHas('employee_payroll_slips', [
            'employee_payroll_id' => $payroll->id,
            'telegram_file_id' => 'fake_telegram_id',
        ]);
    }

    /** PS-04: generate() throws RuntimeException when ConvertApi returns null */
    public function test_ps04_throws_when_convert_api_fails(): void
    {
        $payroll = $this->makePayrollWithItems();

        $this->mock(ConvertApiService::class, fn ($m) => $m->shouldReceive('docxToPdf')->andReturn(null));
        $this->mock(TelegramStorageService::class, fn ($m) => $m->shouldReceive('uploadFile')->never());

        $this->expectException(\RuntimeException::class);

        app(PayrollSlipService::class)->generate($payroll, null, null, PayrollSlipService::TYPE_REGULAR_TUTOR);
    }

    /** PS-05: generate() works for official_tutor template type */
    public function test_ps05_generate_official_tutor_type(): void
    {
        $payroll = $this->makePayrollWithItems();

        $this->mockDependencies();

        $slip = app(PayrollSlipService::class)->generate($payroll, null, null, PayrollSlipService::TYPE_OFFICIAL_TUTOR);

        $this->assertNotNull($slip->telegram_file_id);
    }

    /** PS-06: generate() works for marketing template type */
    public function test_ps06_generate_marketing_type(): void
    {
        $payroll = $this->makePayrollWithItems();

        $this->mockDependencies();

        $slip = app(PayrollSlipService::class)->generate($payroll, null, null, PayrollSlipService::TYPE_MARKETING);

        $this->assertNotNull($slip->telegram_file_id);
    }

    // =========================================================
    // Helpers
    // =========================================================

    private function makePayrollWithItems(): EmployeePayroll
    {
        // Use unique year/month in 2060 range — avoids seeder (2025-2027) and factory (2090+)
        $seq = ++self::$periodSeq;
        $year = 2060 + intdiv($seq - 1, 12);
        $month = (($seq - 1) % 12) + 1;

        $period = PayrollPeriod::factory()->finalized()->create([
            'period_year' => $year,
            'period_month' => $month,
        ]);

        // Direct inserts bypass EmployeeFactory gap-lock deadlock on areas table
        $provinceId = DB::table('provinces')->insertGetId(['name' => 'Province', 'created_at' => now(), 'updated_at' => now()]);
        $regionId = DB::table('regions')->insertGetId(['province_id' => $provinceId, 'name' => 'Region', 'created_at' => now(), 'updated_at' => now()]);
        $areaId = DB::table('areas')->insertGetId(['region_id' => $regionId, 'name' => 'Area', 'created_at' => now(), 'updated_at' => now()]);
        $branchId = DB::table('branches')->insertGetId([
            'areas_id' => $areaId,
            'branch_name' => 'Branch',
            'code_branches' => 'BR'.substr(md5(uniqid()), 0, 6),
            'address' => 'Address',
            'whatsapp' => '6201234567890',
            'latitude' => -6.0, 'longitude' => 106.0, 'radius_meters' => 500, 'is_active' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $employeeId = DB::table('employees')->insertGetId([
            'employee_code' => 'EMP'.substr(md5(uniqid()), 0, 6),
            'full_name' => 'Test Employee',
            'gender' => 'L', 'birthdate' => '1990-01-01',
            'email' => 'slip_'.uniqid().'@test.com',
            'whatsapp_number' => '6201234567890',
            'region_id' => $regionId, 'area_id' => $areaId, 'branch_id' => $branchId,
            'is_active' => true, 'is_hq' => false,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $payroll = EmployeePayroll::factory()->create([
            'payroll_period_id' => $period->id,
            'employee_id' => $employeeId,
            'total_earnings' => 5000000,
            'total_deductions' => 200000,
            'net_amount' => 4800000,
        ]);

        $earningComp = PayrollComponent::factory()->earning()->fixed()->create(['component_code' => 'TEST-ERN-'.$payroll->id]);
        $deductionComp = PayrollComponent::factory()->deduction()->fixed()->create(['component_code' => 'TEST-DED-'.$payroll->id]);

        PayrollItem::factory()->create([
            'employee_payroll_id' => $payroll->id,
            'payroll_component_id' => $earningComp->id,
            'component_type_snapshot' => 'earning',
            'component_code_snapshot' => 'TEST-ERN-'.$payroll->id,
            'component_name_snapshot' => 'Gaji Pokok',
            'quantity' => 1,
            'unit_value' => 5000000,
            'total_amount' => 5000000,
        ]);

        PayrollItem::factory()->create([
            'employee_payroll_id' => $payroll->id,
            'payroll_component_id' => $deductionComp->id,
            'component_type_snapshot' => 'deduction',
            'component_code_snapshot' => 'TEST-DED-'.$payroll->id,
            'component_name_snapshot' => 'Test Deduction',
            'quantity' => 1,
            'unit_value' => 200000,
            'total_amount' => 200000,
        ]);

        return $payroll->load(['employee.user.roles', 'period', 'items.bonusCalculation']);
    }

    /** @return array{ConvertApiService, TelegramStorageService} */
    private function mockDependencies(string $fileId = 'fake_telegram_id'): array
    {
        $convertApi = $this->mock(ConvertApiService::class, fn ($m) => $m->shouldReceive('docxToPdf')->once()->andReturn('%PDF-1.4 fake')
        );

        $telegram = $this->mock(TelegramStorageService::class, fn ($m) => $m->shouldReceive('uploadFile')->once()->andReturn(['file_id' => $fileId])
        );

        return [$convertApi, $telegram];
    }

    private function createSlipTemplates(): void
    {
        $dir = storage_path('app/templates');
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        foreach ([
            PayrollSlipService::TYPE_REGULAR_TUTOR,
            PayrollSlipService::TYPE_OFFICIAL_TUTOR,
            PayrollSlipService::TYPE_MARKETING,
        ] as $type) {
            $this->buildMinimalSlipDocx($type, "{$dir}/slip_{$type}.docx");
        }
    }

    private function buildMinimalSlipDocx(string $type, string $savePath): void
    {
        $phpword = new PhpWord;
        $section = $phpword->addSection();

        // Universal header vars
        $section->addText(implode(' ', [
            '${nama_pegawai}', '${kode_pegawai}', '${posisi_pegawai}', '${role_pegawai}',
            '${periode_gaji}', '${nama_hari}', '${tanggal_bulan_tahun}',
            '${nama_penandatangan}', '${jabatan_penandatangan}',
            '${net_salary}',
            '${total_nominal_seluruh_komponen_pendapatan}',
            '${total_nominal_seluruh_komponen_pengurangan}',
        ]));

        // Type-specific vars
        match ($type) {
            PayrollSlipService::TYPE_REGULAR_TUTOR => $section->addText(
                '${main_salary} ${meeting} ${monthly_meetings} ${gross_salary} ${omzet} ${ex_adm} ${percentage} ${net_bonus}'
            ),
            PayrollSlipService::TYPE_OFFICIAL_TUTOR => $section->addText(
                '${sessions} ${monthly_meetings} ${monthly_attend} ${workdays} ${main_salary} ${gross_salary} ${omzet} ${ex_adm} ${percentage} ${net_bonus}'
            ),
            PayrollSlipService::TYPE_MARKETING => $section->addText(
                '${omzet} ${ex_adm} ${percentage} ${net_bonus}'
            ),
        };

        // Table row for cloneRow('nama_komponen_pendapatan', n)
        $table = $section->addTable();
        $row = $table->addRow();
        $row->addCell(2500)->addText('${nama_komponen_pendapatan}');
        $row->addCell(2500)->addText('${nominal_komponen_pendapatan}');
        $row->addCell(2500)->addText('${nama_komponen_pengurangan}');
        $row->addCell(2500)->addText('${nominal_komponen_pengurangan}');

        $writer = IOFactory::createWriter($phpword, 'Word2007');
        $writer->save($savePath);
    }

    private function deleteSlipTemplates(): void
    {
        $dir = storage_path('app/templates');
        foreach ([
            PayrollSlipService::TYPE_REGULAR_TUTOR,
            PayrollSlipService::TYPE_OFFICIAL_TUTOR,
            PayrollSlipService::TYPE_MARKETING,
        ] as $type) {
            $path = "{$dir}/slip_{$type}.docx";
            if (file_exists($path)) {
                unlink($path);
            }
        }
    }
}
