<?php

namespace Tests\Feature\Payroll;

use App\Models\DocumentSignatureSetting;
use App\Models\Employee;
use App\Models\EmployeePayroll;
use App\Models\EmployeePayrollSlip;
use App\Models\PayrollComponent;
use App\Models\PayrollItem;
use App\Models\PayrollPeriod;
use App\Models\User;
use App\Services\Letter\ConvertApiService;
use App\Services\Payroll\PayrollSlipService;
use App\Services\TelegramStorageService;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use Tests\TestCase;

class PayrollSlipTest extends TestCase
{
    private User $adminUser;

    private int $adminEmployeeId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminEmployeeId = $this->insertEmployee('GS-ADMIN');
        $this->adminUser = User::factory()->create(['employee_id' => $this->adminEmployeeId]);
        $this->adminUser->givePermissionTo([
            'payroll.period.view',
            'payroll.slip.generate',
        ]);

        $this->createSlipTemplates();
    }

    protected function tearDown(): void
    {
        $this->deleteSlipTemplates();
        parent::tearDown();
    }

    /**
     * Direct DB insert for employee — bypasses factory FK deadlock chain.
     * Creates Province→Region→Area→Branch→Employee chain, returns employee ID.
     */
    private function insertEmployee(string $prefix = 'EMP'): int
    {
        $provinceId = DB::table('provinces')->insertGetId(['name' => "Prov {$prefix}", 'created_at' => now(), 'updated_at' => now()]);
        $regionId = DB::table('regions')->insertGetId(['province_id' => $provinceId, 'name' => "Reg {$prefix}", 'created_at' => now(), 'updated_at' => now()]);
        $areaId = DB::table('areas')->insertGetId(['region_id' => $regionId, 'name' => "Area {$prefix}", 'created_at' => now(), 'updated_at' => now()]);
        $branchId = DB::table('branches')->insertGetId([
            'areas_id' => $areaId,
            'branch_name' => "Branch {$prefix}",
            'code_branches' => 'BR'.substr(md5(uniqid($prefix)), 0, 6),
            'address' => 'Test Address',
            'whatsapp' => '62'.fake()->numerify('###########'),
            'latitude' => -6.0, 'longitude' => 106.0, 'radius_meters' => 500, 'is_active' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        return DB::table('employees')->insertGetId([
            'employee_code' => 'EMP'.substr(md5(uniqid($prefix)), 0, 6),
            'full_name' => "Employee {$prefix}",
            'gender' => 'L', 'birthdate' => '1990-01-01',
            'email' => strtolower($prefix).'_'.uniqid().'@test.com',
            'whatsapp_number' => '62'.fake()->numerify('###########'),
            'region_id' => $regionId, 'area_id' => $areaId, 'branch_id' => $branchId,
            'is_active' => false,
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    // ============================================================
    // GS-04: Manual generate slip — route exists
    // ============================================================

    /** GS-04: Generate slip route is registered and accessible */
    public function test_gs04_generate_slip_route_exists(): void
    {
        $this->assertNotNull(route('payroll.payrolls.slip.generate', [
            'period' => 1,
            'payroll' => 1,
        ]));
    }

    // ============================================================
    // GS-05: Download slip — route exists
    // ============================================================

    /** GS-05: Download slip route is registered */
    public function test_gs05_download_slip_route_exists(): void
    {
        $this->assertNotNull(route('payroll.payrolls.slip.download', [
            'period' => 1,
            'payroll' => 1,
        ]));
    }

    // ============================================================
    // GS-06: Manual generate — without permission blocked
    // ============================================================

    /** GS-06: Generate slip without payroll.slip.generate permission → 403 */
    public function test_gs06_generate_without_permission_blocked(): void
    {
        $userWithoutPermission = User::factory()->create(['employee_id' => null]);

        [$period, $payroll] = $this->createFinalizedPeriodWithPayroll();

        $response = $this->actingAs($userWithoutPermission)
            ->post(route('payroll.payrolls.slip.generate', [$period, $payroll]));

        $response->assertForbidden();
    }

    // ============================================================
    // GS-07: Download slip — not yet generated
    // ============================================================

    /** GS-07: Download slip when no slip exists → redirect with error */
    public function test_gs07_download_slip_not_yet_generated(): void
    {
        [$period, $payroll] = $this->createFinalizedPeriodWithPayroll();

        $response = $this->actingAs($this->adminUser)
            ->get(route('payroll.payrolls.slip.download', [$period, $payroll]));

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    // ============================================================
    // GS-08: Download slip — with Telegram file
    // ============================================================

    /** GS-08: Download slip when Telegram file exists → file download response */
    public function test_gs08_download_slip_with_telegram_file(): void
    {
        [$period, $payroll] = $this->createFinalizedPeriodWithPayroll();

        EmployeePayrollSlip::create([
            'employee_payroll_id' => $payroll->id,
            'telegram_file_id' => 'test_file_id_123',
            'generated_at' => now(),
            'generated_by_employee_id' => $this->adminEmployeeId,
            'signatory_name_snapshot' => 'Test Signer',
            'signatory_position_snapshot' => 'Director',
            'signed_at' => now(),
        ]);

        $this->mock(TelegramStorageService::class, fn ($m) => $m->shouldReceive('downloadFile')
            ->with('test_file_id_123')
            ->once()
            ->andReturn('fake slip content')
        );

        $response = $this->actingAs($this->adminUser)
            ->get(route('payroll.payrolls.slip.download', [$period, $payroll]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition');
    }

    // ============================================================
    // GS-09: Output Content-Type is text/plain (GAP-115)
    // ============================================================

    /** GS-09: Download Content-Type — GAP-115 was fixed: now returns application/pdf */
    public function test_gs09_download_content_type_is_pdf(): void
    {
        [$period, $payroll] = $this->createFinalizedPeriodWithPayroll();

        EmployeePayrollSlip::create([
            'employee_payroll_id' => $payroll->id,
            'telegram_file_id' => 'test_file_id_ct',
            'generated_at' => now(),
            'signatory_name_snapshot' => 'Test',
            'signatory_position_snapshot' => 'Test',
            'signed_at' => now(),
        ]);

        $this->mock(TelegramStorageService::class, fn ($m) => $m->shouldReceive('downloadFile')
            ->andReturn('%PDF-1.4 fake pdf content')
        );

        $response = $this->actingAs($this->adminUser)
            ->get(route('payroll.payrolls.slip.download', [$period, $payroll]));

        $contentType = $response->headers->get('Content-Type');
        $this->assertStringContainsString('application/pdf', $contentType,
            'GAP-115 resolved: Content-Type is now application/pdf');
    }

    // ============================================================
    // GS-10: DocumentSignatureSetting type=kwitansi used
    // ============================================================

    /** GS-10: Signatory from DocumentSignatureSetting type=kwitansi */
    public function test_gs10_signatory_from_document_signature_setting(): void
    {
        DocumentSignatureSetting::firstOrCreate(
            ['document_type' => 'kwitansi'],
            ['signer_name' => 'Dr. Budi Santoso', 'signer_title' => 'Direktur Utama']
        );

        [$period, $payroll] = $this->createFinalizedPeriodWithPayroll();
        $this->mockSlipDependencies();

        $response = $this->actingAs($this->adminUser)
            ->post(route('payroll.payrolls.slip.generate', [$period, $payroll]));

        $response->assertRedirect();

        $slip = EmployeePayrollSlip::where('employee_payroll_id', $payroll->id)->first();
        $this->assertNotNull($slip);
        $this->assertEquals('Dr. Budi Santoso', $slip->signatory_name_snapshot);
        $this->assertEquals('Direktur Utama', $slip->signatory_position_snapshot);
    }

    // ============================================================
    // GS-11: No DocumentSignatureSetting — null signatory
    // ============================================================

    /** GS-11: Slip generated with null signatory when no DocumentSignatureSetting */
    public function test_gs11_no_document_signature_setting_null_signatory(): void
    {
        [$period, $payroll] = $this->createFinalizedPeriodWithPayroll();
        $this->mockSlipDependencies();

        $response = $this->actingAs($this->adminUser)
            ->post(route('payroll.payrolls.slip.generate', [$period, $payroll]));

        $response->assertRedirect();

        $slip = EmployeePayrollSlip::where('employee_payroll_id', $payroll->id)->first();
        $this->assertNotNull($slip);
        $this->assertNull($slip->signatory_employee_id);
    }

    // ============================================================
    // GS-12: signatory_employee_id from request is used
    // ============================================================

    /** GS-12: signatory_employee_id from request is passed to service */
    public function test_gs12_signatory_employee_id_from_request_used(): void
    {
        [$period, $payroll] = $this->createFinalizedPeriodWithPayroll();

        $signatoryEmployeeId = $this->insertEmployee('GS-SIG');
        $this->mockSlipDependencies();

        $response = $this->actingAs($this->adminUser)
            ->post(route('payroll.payrolls.slip.generate', [$period, $payroll]), [
                'signatory_employee_id' => $signatoryEmployeeId,
            ]);

        $response->assertRedirect();

        $slip = EmployeePayrollSlip::where('employee_payroll_id', $payroll->id)->first();
        $this->assertNotNull($slip);
        $this->assertEquals($signatoryEmployeeId, $slip->signatory_employee_id);
        // Fetch the employee name from DB since we used DB::table insert
        $signatoryName = DB::table('employees')->where('id', $signatoryEmployeeId)->value('full_name');
        $this->assertEquals($signatoryName, $slip->signatory_name_snapshot);
    }

    // ============================================================
    // GS-13: Regenerate slip — updateOrCreate
    // ============================================================

    /** GS-13: Regenerate slip overwrites existing telegram_file_id */
    public function test_gs13_regenerate_slip_overwrites_existing(): void
    {
        [$period, $payroll] = $this->createFinalizedPeriodWithPayroll();

        $callCount = 0;
        $this->mock(TelegramStorageService::class, fn ($m) => $m->shouldReceive('uploadFile')
            ->twice()
            ->andReturnUsing(function () use (&$callCount) {
                $callCount++;

                return ['file_id' => $callCount === 1 ? 'first_file_id' : 'second_file_id'];
            })
        );

        $this->mock(ConvertApiService::class, fn ($m) => $m->shouldReceive('docxToPdf')
            ->twice()
            ->andReturn('%PDF-1.4 fake')
        );

        $this->mock(\App\Services\Payroll\PayrollNotificationService::class, fn ($m) => $m->shouldReceive('notifySlipReady')
            ->twice()
        );

        $this->actingAs($this->adminUser)
            ->post(route('payroll.payrolls.slip.generate', [$period, $payroll]));

        $this->assertEquals(1, EmployeePayrollSlip::where('employee_payroll_id', $payroll->id)->count());
        $this->assertEquals('first_file_id', EmployeePayrollSlip::where('employee_payroll_id', $payroll->id)->value('telegram_file_id'));

        app()->forgetInstance(\App\Http\Controllers\Admin\Payroll\PayrollSlipController::class);

        $this->actingAs($this->adminUser)
            ->post(route('payroll.payrolls.slip.generate', [$period, $payroll]));

        $this->assertEquals(1, EmployeePayrollSlip::where('employee_payroll_id', $payroll->id)->count());
        $this->assertEquals('second_file_id', EmployeePayrollSlip::where('employee_payroll_id', $payroll->id)->value('telegram_file_id'));
    }

    // ============================================================
    // GS-14: notifySlipReady called during generate
    // ============================================================

    /** GS-14: notifySlipReady is called during slip generation */
    public function test_gs14_notify_slip_ready_called(): void
    {
        [$period, $payroll] = $this->createFinalizedPeriodWithPayroll();

        $capturedPayrollId = null;
        $mockNotification = \Mockery::mock(\App\Services\Payroll\PayrollNotificationService::class);
        $mockNotification->shouldReceive('notifySlipReady')
            ->once()
            ->andReturnUsing(function (EmployeePayroll $arg) use (&$capturedPayrollId) {
                $capturedPayrollId = $arg->id;
            });
        $this->app->instance(\App\Services\Payroll\PayrollNotificationService::class, $mockNotification);

        $this->mockSlipDependencies();
        app()->forgetInstance(\App\Http\Controllers\Admin\Payroll\PayrollSlipController::class);

        $response = $this->actingAs($this->adminUser)
            ->post(route('payroll.payrolls.slip.generate', [$period, $payroll]));

        $response->assertRedirect();

        $this->assertNotNull(
            EmployeePayrollSlip::where('employee_payroll_id', $payroll->id)->first(),
            'Slip should have been generated'
        );

        $this->assertEquals($payroll->id, $capturedPayrollId,
            'notifySlipReady should be called with the payroll — GAP-117: runs synchronously via Mail::raw');
    }

    // ============================================================
    // GS-15: Mail::raw used instead of SendPayrollSlipJob
    // ============================================================

    /** GS-15: Email notification uses Mail::raw, not a dedicated Job */
    public function test_gs15_email_notification_uses_mail_raw(): void
    {
        [$period, $payroll] = $this->createFinalizedPeriodWithPayroll();

        // Update employee email so notification would be triggered — use unique email
        $uniqueEmail = 'employee_'.uniqid().'@test.example';
        DB::table('employees')->where('id', $payroll->employee_id)->update(['email' => $uniqueEmail]);

        $notificationCalled = false;
        $mockNotification = \Mockery::mock(\App\Services\Payroll\PayrollNotificationService::class);
        $mockNotification->shouldReceive('notifySlipReady')
            ->once()
            ->andReturnUsing(function () use (&$notificationCalled) {
                $notificationCalled = true;
            });
        $this->app->instance(\App\Services\Payroll\PayrollNotificationService::class, $mockNotification);

        $this->mockSlipDependencies();
        app()->forgetInstance(\App\Http\Controllers\Admin\Payroll\PayrollSlipController::class);

        $this->actingAs($this->adminUser)
            ->post(route('payroll.payrolls.slip.generate', [$period, $payroll]));

        $this->assertFalse(class_exists(\App\Jobs\SendPayrollSlipJob::class),
            'GAP-117: SendPayrollSlipJob does not exist — notification is synchronous via Mail::raw');

        $this->assertTrue($notificationCalled,
            'notifySlipReady was called synchronously during the request — GAP-117: no queue');
    }

    // ============================================================
    // Additional: signatory_employee_id invalid validation
    // ============================================================

    /** GS-16: Invalid signatory_employee_id rejected by validation */
    public function test_gs16_invalid_signatory_employee_id_rejected(): void
    {
        [$period, $payroll] = $this->createFinalizedPeriodWithPayroll();

        $response = $this->actingAs($this->adminUser)
            ->post(route('payroll.payrolls.slip.generate', [$period, $payroll]), [
                'signatory_employee_id' => 999999,
            ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('signatory_employee_id');
    }

    // ============================================================
    // Helpers
    // ============================================================

    private function createFinalizedPeriodWithPayroll(float $netAmount = 5000000): array
    {
        // Use firstOrCreate to avoid unique constraint collisions from leftover rows
        // after RefreshDatabase rollback failures
        $seq = fake()->numberBetween(25080, 30000);
        $year = intdiv($seq, 12);
        $month = ($seq % 12) + 1;

        $period = PayrollPeriod::firstOrCreate(
            ['period_month' => $month, 'period_year' => $year],
            [
                'status' => 'finalized',
                'confirmed_by_employee_id' => $this->adminEmployeeId,
                'confirmed_at' => now(),
            ]
        );
        // Ensure it's finalized even if it already existed as draft/review
        if ($period->status !== 'finalized') {
            $period->update(['status' => 'finalized', 'confirmed_by_employee_id' => $this->adminEmployeeId, 'confirmed_at' => now()]);
        }

        $employeeId = $this->insertEmployee('GS-EMP-'.uniqid());
        $payroll = EmployeePayroll::factory()->create([
            'payroll_period_id' => $period->id,
            'employee_id' => $employeeId,
            'total_earnings' => $netAmount,
            'total_deductions' => 0,
            'net_amount' => $netAmount,
            'payment_status' => 'unpaid',
        ]);

        $earningComp = PayrollComponent::factory()->earning()->fixed()->create(['component_code' => 'TEST-ERN-'.$payroll->id]);
        PayrollItem::factory()->create([
            'employee_payroll_id' => $payroll->id,
            'payroll_component_id' => $earningComp->id,
            'component_type_snapshot' => 'earning',
            'component_code_snapshot' => 'TEST-ERN-'.$payroll->id,
            'component_name_snapshot' => 'Gaji Pokok',
            'quantity' => 1,
            'unit_value' => $netAmount,
            'total_amount' => $netAmount,
        ]);

        return [$period, $payroll->load(['employee.user.roles', 'period', 'items.bonusCalculation'])];
    }

    private function mockSlipDependencies(string $fileId = 'fake_telegram_id'): void
    {
        $this->mock(ConvertApiService::class, fn ($m) => $m->shouldReceive('docxToPdf')
            ->andReturn('%PDF-1.4 fake')
        );

        $this->mock(TelegramStorageService::class, fn ($m) => $m->shouldReceive('uploadFile')
            ->andReturn(['file_id' => $fileId])
        );
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
            $path = "{$dir}/slip_{$type}.docx";
            if (! file_exists($path)) {
                $this->buildMinimalSlipDocx($type, $path);
            }
        }
    }

    private function buildMinimalSlipDocx(string $type, string $savePath): void
    {
        $phpword = new PhpWord;
        $section = $phpword->addSection();

        $section->addText(implode(' ', [
            '${nama_pegawai}', '${kode_pegawai}', '${posisi_pegawai}', '${role_pegawai}',
            '${periode_gaji}', '${nama_hari}', '${tanggal_bulan_tahun}',
            '${nama_penandatangan}', '${jabatan_penandatangan}',
            '${net_salary}',
            '${total_nominal_seluruh_komponen_pendapatan}',
            '${total_nominal_seluruh_komponen_pengurangan}',
        ]));

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
                @unlink($path);
            }
        }
    }
}
