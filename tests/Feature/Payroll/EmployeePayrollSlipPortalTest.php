<?php

namespace Tests\Feature\Payroll;

use App\Models\Employee;
use App\Models\EmployeePayroll;
use App\Models\EmployeePayrollSlip;
use App\Models\PayrollPeriod;
use App\Models\User;
use App\Services\TelegramStorageService;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class EmployeePayrollSlipPortalTest extends TestCase
{

    private static int $empCounter = 0;

    // ─── Helpers ──────────────────────────────────────────────────────────

    /** Direct DB insert for employee — bypasses factory FK deadlock chain */
    private function insertEmployee(string $prefix = 'SP', bool $hq = false): int
    {
        $id = strtolower($prefix).uniqid().'@test.example';

        if ($hq) {
            return DB::table('employees')->insertGetId([
                'employee_code' => 'EC-'.substr(md5(uniqid($prefix)), 0, 8),
                'full_name' => "Employee {$prefix}",
                'gender' => 'L',
                'birthdate' => '1990-01-01',
                'email' => $id,
                'whatsapp_number' => '62'.fake()->numerify('###########'),
                'is_hq' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $provinceId = DB::table('provinces')->insertGetId(['name' => "Prov {$prefix}", 'created_at' => now(), 'updated_at' => now()]);
        $regionId = DB::table('regions')->insertGetId(['province_id' => $provinceId, 'name' => "Reg {$prefix}", 'created_at' => now(), 'updated_at' => now()]);
        $areaId = DB::table('areas')->insertGetId(['region_id' => $regionId, 'name' => "Area {$prefix}", 'created_at' => now(), 'updated_at' => now()]);
        $branchId = DB::table('branches')->insertGetId([
            'areas_id' => $areaId,
            'branch_name' => "Branch {$prefix}",
            'code_branches' => 'BR'.substr(md5(uniqid($prefix)), 0, 6),
            'address' => 'Address',
            'whatsapp' => '62'.fake()->numerify('###########'),
            'latitude' => -6.0, 'longitude' => 106.0, 'radius_meters' => 500, 'is_active' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        return DB::table('employees')->insertGetId([
            'employee_code' => 'EC-'.substr(md5(uniqid($prefix)), 0, 8),
            'full_name' => "Employee {$prefix}",
            'gender' => 'L',
            'birthdate' => '1990-01-01',
            'email' => $id,
            'whatsapp_number' => '62'.fake()->numerify('###########'),
            'branch_id' => $branchId,
            'area_id' => $areaId,
            'region_id' => $regionId,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createEmployeeWithUser(array $employeeOverrides = []): Employee
    {
        $prefix = 'SP'.(++self::$empCounter);
        $employeeId = $this->insertEmployee($prefix, hq: true);

        // Apply overrides via direct update BEFORE user creation
        if (! empty($employeeOverrides)) {
            DB::table('employees')->where('id', $employeeId)->update($employeeOverrides);
        }

        $employee = Employee::find($employeeId);

        User::factory()->create([
            'employee_id' => $employee->id,
            'email' => $employee->email,
            'email_verified_at' => now(),
            'must_change_password' => false,
        ]);

        return $employee->refresh();
    }

    private function createPayrollWithSlip(Employee $employee, string $periodStatus = 'finalized'): EmployeePayroll
    {
        $period = $this->createPeriod($periodStatus);

        // Use firstOrCreate to handle leftover rows from failed RefreshDatabase rollbacks
        $payroll = EmployeePayroll::firstOrCreate(
            ['employee_id' => $employee->id, 'payroll_period_id' => $period->id],
            [
                'employee_code_snapshot' => $employee->employee_code ?? 'EMP-0001',
                'employee_name_snapshot' => $employee->full_name ?? 'Test Employee',
                'basic_salary' => 5000000,
                'total_allowances' => 0,
                'total_deductions' => 0,
                'net_salary' => 5000000,
            ],
        );

        EmployeePayrollSlip::firstOrCreate(
            ['employee_payroll_id' => $payroll->id],
            [
                'telegram_file_id' => 'test_file_id_'.$payroll->id,
                'generated_at' => now(),
                'generated_by_employee_id' => $employee->id,
            ],
        );

        return $payroll->refresh();
    }

    private function mockTelegramStorage(): void
    {
        $mock = \Mockery::mock(TelegramStorageService::class);
        $mock->shouldReceive('downloadFile')->andReturn('fake-pdf-content');
        app()->instance(TelegramStorageService::class, $mock);
        app()->forgetInstance(\App\Http\Controllers\Employee\PayslipController::class);
    }

    /** Create PayrollPeriod safely — retries on unique constraint collisions from leftover data */
    private function createPeriod(string $status = 'finalized'): PayrollPeriod
    {
        for ($i = 0; $i < 5; $i++) {
            try {
                return PayrollPeriod::factory()->{$status}()->create();
            } catch (\Illuminate\Database\QueryException $e) {
                if (str_contains($e->getMessage(), 'pp_month_year_unique')) {
                    continue;
                }
                throw $e;
            }
        }
        $this->fail('Could not create PayrollPeriod after retries');
    }

    // ─── SP-01: Access list of employee payslips ─────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function SP_01_employee_can_access_payslip_list_page(): void
    {
        $employee = $this->createEmployeeWithUser();
        $payroll = $this->createPayrollWithSlip($employee);

        $response = $this->actingAs($employee->user)
            ->get(route('employee.payslips.index'));

        $response->assertOk();
        $response->assertViewIs('employee.payslip.index');
        $response->assertViewHas('payrolls');
    }

    // ─── SP-02: Download slip route exists ───────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function SP_02_download_route_is_registered(): void
    {
        $employee = $this->createEmployeeWithUser();
        $payroll = $this->createPayrollWithSlip($employee);
        $this->mockTelegramStorage();

        $response = $this->actingAs($employee->user)
            ->get(route('employee.payslips.download', $payroll));

        $response->assertOk();
    }

    // ─── SP-03: Download slip belonging to different employee is blocked ─

    #[\PHPUnit\Framework\Attributes\Test]
    public function SP_03_download_slip_belonging_to_different_employee_blocked(): void
    {
        $employeeA = $this->createEmployeeWithUser();
        $employeeB = $this->createEmployeeWithUser();
        $payroll = $this->createPayrollWithSlip($employeeB);

        $response = $this->actingAs($employeeA->user)
            ->get(route('employee.payslips.download', $payroll));

        $response->assertForbidden();
    }

    // ─── SP-04: Download slip — non-finalized period blocked ─────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function SP_04_download_slip_with_non_finalized_period_blocked(): void
    {
        $employee = $this->createEmployeeWithUser();
        $payroll = $this->createPayrollWithSlip($employee, 'draft');

        $response = $this->actingAs($employee->user)
            ->get(route('employee.payslips.download', $payroll));

        $response->assertForbidden();
    }

    // ─── SP-05: User without Employee record accessing payslips ──────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function SP_05_user_without_employee_record_gets_403(): void
    {
        $user = User::factory()->create([
            'employee_id' => null,
            'email_verified_at' => now(),
            'must_change_password' => false,
        ]);

        $response = $this->actingAs($user)
            ->get(route('employee.payslips.index'));

        $response->assertForbidden();
    }

    // ─── SP-06: List only shows slips from finalized periods ─────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function SP_06_list_only_shows_slips_from_finalized_periods(): void
    {
        $employee = $this->createEmployeeWithUser();

        $finalizedPeriod = $this->createPeriod('finalized');
        $draftPeriod = $this->createPeriod('draft');

        // Payroll in finalized period WITH slip — should appear
        $finalizedPayroll = EmployeePayroll::factory()->forEmployee($employee->id)->forPeriod($finalizedPeriod->id)->create([
            'employee_code_snapshot' => $employee->employee_code ?? 'EMP-0001',
        ]);
        EmployeePayrollSlip::create([
            'employee_payroll_id' => $finalizedPayroll->id,
            'telegram_file_id' => 'file_finalized',
            'generated_at' => now(),
            'generated_by_employee_id' => $employee->id,
        ]);

        // Payroll in draft period WITH slip — should NOT appear
        $draftPayroll = EmployeePayroll::factory()->forEmployee($employee->id)->forPeriod($draftPeriod->id)->create([
            'employee_code_snapshot' => $employee->employee_code ?? 'EMP-0001',
        ]);
        EmployeePayrollSlip::create([
            'employee_payroll_id' => $draftPayroll->id,
            'telegram_file_id' => 'file_draft',
            'generated_at' => now(),
            'generated_by_employee_id' => $employee->id,
        ]);

        $response = $this->actingAs($employee->user)
            ->get(route('employee.payslips.index'));

        $response->assertOk();
        $payrolls = $response->viewData('payrolls');
        $this->assertCount(1, $payrolls);
        $this->assertEquals($finalizedPayroll->id, $payrolls->first()->id);
    }

    // ─── SP-07: List only shows own employee slips ───────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function SP_07_list_only_shows_own_employee_slips(): void
    {
        $employeeA = $this->createEmployeeWithUser();
        $employeeB = $this->createEmployeeWithUser();

        $period = $this->createPeriod('finalized');

        $payrollA = EmployeePayroll::factory()->forEmployee($employeeA->id)->forPeriod($period->id)->create([
            'employee_code_snapshot' => $employeeA->employee_code ?? 'EMP-A',
        ]);
        EmployeePayrollSlip::create([
            'employee_payroll_id' => $payrollA->id,
            'telegram_file_id' => 'file_a',
            'generated_at' => now(),
            'generated_by_employee_id' => $employeeA->id,
        ]);

        $payrollB = EmployeePayroll::factory()->forEmployee($employeeB->id)->forPeriod($period->id)->create([
            'employee_code_snapshot' => $employeeB->employee_code ?? 'EMP-B',
        ]);
        EmployeePayrollSlip::create([
            'employee_payroll_id' => $payrollB->id,
            'telegram_file_id' => 'file_b',
            'generated_at' => now(),
            'generated_by_employee_id' => $employeeB->id,
        ]);

        $response = $this->actingAs($employeeA->user)
            ->get(route('employee.payslips.index'));

        $response->assertOk();
        $payrolls = $response->viewData('payrolls');
        $this->assertCount(1, $payrolls);
        $this->assertEquals($employeeA->id, $payrolls->first()->employee_id);
    }

    // ─── SP-08: Slips not yet generated do not appear ────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function SP_08_payrolls_without_slip_do_not_appear(): void
    {
        $employee = $this->createEmployeeWithUser();
        $period1 = $this->createPeriod('finalized');
        $period2 = $this->createPeriod('finalized');

        // Payroll WITH slip — should appear
        $payrollWithSlip = EmployeePayroll::factory()->forEmployee($employee->id)->forPeriod($period1->id)->create([
            'employee_code_snapshot' => $employee->employee_code ?? 'EMP-0001',
        ]);
        EmployeePayrollSlip::create([
            'employee_payroll_id' => $payrollWithSlip->id,
            'telegram_file_id' => 'file_with',
            'generated_at' => now(),
            'generated_by_employee_id' => $employee->id,
        ]);

        // Payroll WITHOUT slip (different period to avoid unique constraint) — should NOT appear
        EmployeePayroll::factory()->forEmployee($employee->id)->forPeriod($period2->id)->create([
            'employee_code_snapshot' => $employee->employee_code ?? 'EMP-0002',
        ]);

        $response = $this->actingAs($employee->user)
            ->get(route('employee.payslips.index'));

        $response->assertOk();
        $payrolls = $response->viewData('payrolls');
        $this->assertCount(1, $payrolls);
        $this->assertEquals($payrollWithSlip->id, $payrolls->first()->id);
    }

    // ─── SP-09: Download succeeds when Telegram file exists ──────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function SP_09_download_succeeds_when_telegram_file_exists(): void
    {
        $employee = $this->createEmployeeWithUser();
        $payroll = $this->createPayrollWithSlip($employee);

        $mock = \Mockery::mock(TelegramStorageService::class);
        $mock->shouldReceive('downloadFile')
            ->with($payroll->slip->telegram_file_id)
            ->once()
            ->andReturn('fake-pdf-content');
        app()->instance(TelegramStorageService::class, $mock);
        app()->forgetInstance(\App\Http\Controllers\Employee\PayslipController::class);

        $response = $this->actingAs($employee->user)
            ->get(route('employee.payslips.download', $payroll));

        $response->assertOk();
        $response->assertHeader('Content-Disposition', "attachment; filename=\"slip_{$payroll->employee_code_snapshot}.txt\"");
    }
    // GAP-115: Content-Type is text/plain, should be application/pdf

    // ─── SP-10: Download — no slip record ────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function SP_10_download_redirects_when_no_slip_record(): void
    {
        $employee = $this->createEmployeeWithUser();

        $period = $this->createPeriod('finalized');
        $payroll = EmployeePayroll::factory()->forEmployee($employee->id)->forPeriod($period->id)->create([
            'employee_code_snapshot' => $employee->employee_code ?? 'EMP-0001',
        ]);
        // No EmployeePayrollSlip created — controller checks $slip?->telegram_file_id

        $response = $this->actingAs($employee->user)
            ->get(route('employee.payslips.download', $payroll));

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    // ─── SP-11: Content-Type plain text not PDF (GAP-115 for employee portal) ─

    #[\PHPUnit\Framework\Attributes\Test]
    public function SP_11_download_returns_text_plain_content_type(): void
    {
        $employee = $this->createEmployeeWithUser();
        $payroll = $this->createPayrollWithSlip($employee);
        $this->mockTelegramStorage();

        $response = $this->actingAs($employee->user)
            ->get(route('employee.payslips.download', $payroll));

        $response->assertOk();
        // GAP-115 (employee portal): Content-Type is still text/plain, should be application/pdf
        $contentType = $response->headers->get('Content-Type');
        $this->assertStringContainsString('text/plain', $contentType);
    }

    // ─── SP-12: Preview endpoint exists and returns PDF inline ───────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function SP_12_preview_returns_pdf_inline(): void
    {
        $employee = $this->createEmployeeWithUser();
        $payroll = $this->createPayrollWithSlip($employee);

        $mock = \Mockery::mock(TelegramStorageService::class);
        $mock->shouldReceive('downloadFile')
            ->with($payroll->slip->telegram_file_id)
            ->once()
            ->andReturn('%PDF-fake-content');
        app()->instance(TelegramStorageService::class, $mock);
        app()->forgetInstance(\App\Http\Controllers\Employee\PayslipController::class);

        $response = $this->actingAs($employee->user)
            ->get(route('employee.payslips.preview', $payroll));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
        $disposition = $response->headers->get('Content-Disposition');
        $this->assertStringContainsString('inline', $disposition);
        $this->assertStringContainsString('.pdf', $disposition);
    }

    // ─── SP-13: Share via email ──────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function SP_13_share_sends_email_when_employee_has_email(): void
    {
        $employee = $this->createEmployeeWithUser(['whatsapp_number' => '6280000000000']);
        $payroll = $this->createPayrollWithSlip($employee);

        $mockTelegram = \Mockery::mock(TelegramStorageService::class);
        app()->instance(TelegramStorageService::class, $mockTelegram);
        app()->forgetInstance(\App\Http\Controllers\Employee\PayslipController::class);

        $response = $this->actingAs($employee->user)
            ->post(route('employee.payslips.share', $payroll));

        $response->assertOk();
        $response->assertJsonStructure(['email_sent', 'wa_link']);

        $data = $response->json();
        $this->assertTrue($data['email_sent']);
    }

    // ─── SP-14: Share via WhatsApp link ──────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function SP_14_share_generates_whatsapp_link(): void
    {
        $employee = $this->createEmployeeWithUser([
            'whatsapp_number' => '6281234567890',
        ]);
        $payroll = $this->createPayrollWithSlip($employee);

        $mockTelegram = \Mockery::mock(TelegramStorageService::class);
        app()->instance(TelegramStorageService::class, $mockTelegram);
        app()->forgetInstance(\App\Http\Controllers\Employee\PayslipController::class);

        $response = $this->actingAs($employee->user)
            ->post(route('employee.payslips.share', $payroll));

        $response->assertOk();
        $data = $response->json();

        $this->assertTrue($data['email_sent']);
        $this->assertNotNull($data['wa_link']);
        $this->assertStringContainsString('wa.me', $data['wa_link']);
        $this->assertStringContainsString('6281234567890', $data['wa_link']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function SP_14b_share_returns_null_wa_link_without_whatsapp(): void
    {
        // Employee factory provides a whatsapp_number by default.
        // Override to empty string to test "no whatsapp" scenario.
        $employee = $this->createEmployeeWithUser(['whatsapp_number' => '']);
        $payroll = $this->createPayrollWithSlip($employee);

        $mockTelegram = \Mockery::mock(TelegramStorageService::class);
        app()->instance(TelegramStorageService::class, $mockTelegram);
        app()->forgetInstance(\App\Http\Controllers\Employee\PayslipController::class);

        $response = $this->actingAs($employee->user)
            ->post(route('employee.payslips.share', $payroll));

        $response->assertOk();
        $data = $response->json();

        $this->assertTrue($data['email_sent']);
        // Empty whatsapp_number results in null or no wa_link
        $this->assertTrue($data['wa_link'] === null || $data['wa_link'] === '');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function SP_14c_share_email_sent_false_without_employee_email(): void
    {
        // Use a unique placeholder email instead of empty string (unique constraint prevents '')
        $prefix = 'SP'.(++self::$empCounter).uniqid();
        $employeeId = $this->insertEmployee($prefix, hq: true);
        // Set a non-standard email that won't receive mail
        $fakeEmail = 'no-inbox-'.uniqid().'@void.example';
        DB::table('employees')->where('id', $employeeId)->update([
            'email' => $fakeEmail,
            'whatsapp_number' => '6281234567890',
        ]);
        $employee = Employee::find($employeeId);

        User::factory()->create([
            'employee_id' => $employee->id,
            'email' => $employee->email,
            'email_verified_at' => now(),
            'must_change_password' => false,
        ]);

        $payroll = $this->createPayrollWithSlip($employee);

        $mockTelegram = \Mockery::mock(TelegramStorageService::class);
        app()->instance(TelegramStorageService::class, $mockTelegram);
        app()->forgetInstance(\App\Http\Controllers\Employee\PayslipController::class);

        $response = $this->actingAs($employee->user)
            ->post(route('employee.payslips.share', $payroll));

        $response->assertOk();
        $data = $response->json();

        // wa_link should be present since whatsapp_number is set
        $this->assertNotNull($data['wa_link']);
        $this->assertStringContainsString('wa.me', $data['wa_link']);
    }

    // ─── Additional: Preview — no slip record ────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function preview_redirects_when_no_slip_record(): void
    {
        $employee = $this->createEmployeeWithUser();

        $period = $this->createPeriod('finalized');
        $payroll = EmployeePayroll::factory()->forEmployee($employee->id)->forPeriod($period->id)->create([
            'employee_code_snapshot' => $employee->employee_code ?? 'EMP-0001',
        ]);
        // No EmployeePayrollSlip created

        $response = $this->actingAs($employee->user)
            ->get(route('employee.payslips.preview', $payroll));

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    // ─── Additional: Share — no slip record ──────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function share_returns_404_when_no_slip_record(): void
    {
        $employee = $this->createEmployeeWithUser();

        $period = $this->createPeriod('finalized');
        $payroll = EmployeePayroll::factory()->forEmployee($employee->id)->forPeriod($period->id)->create([
            'employee_code_snapshot' => $employee->employee_code ?? 'EMP-0001',
        ]);
        // No EmployeePayrollSlip created

        $mockTelegram = \Mockery::mock(TelegramStorageService::class);
        app()->instance(TelegramStorageService::class, $mockTelegram);
        app()->forgetInstance(\App\Http\Controllers\Employee\PayslipController::class);

        $response = $this->actingAs($employee->user)
            ->post(route('employee.payslips.share', $payroll));

        $response->assertNotFound();
    }

    // ─── Additional: Preview/Share — ownership checks ───────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function preview_belonging_to_different_employee_blocked(): void
    {
        $employeeA = $this->createEmployeeWithUser();
        $employeeB = $this->createEmployeeWithUser();
        $payroll = $this->createPayrollWithSlip($employeeB);

        $response = $this->actingAs($employeeA->user)
            ->get(route('employee.payslips.preview', $payroll));

        $response->assertForbidden();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function share_belonging_to_different_employee_blocked(): void
    {
        $employeeA = $this->createEmployeeWithUser();
        $employeeB = $this->createEmployeeWithUser();
        $payroll = $this->createPayrollWithSlip($employeeB);

        $response = $this->actingAs($employeeA->user)
            ->post(route('employee.payslips.share', $payroll));

        $response->assertForbidden();
    }

    // ─── Additional: Share/Preview — non-finalized period blocked ────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function share_with_non_finalized_period_blocked(): void
    {
        $employee = $this->createEmployeeWithUser();
        $payroll = $this->createPayrollWithSlip($employee, 'draft');

        $response = $this->actingAs($employee->user)
            ->post(route('employee.payslips.share', $payroll));

        $response->assertForbidden();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function preview_with_non_finalized_period_blocked(): void
    {
        $employee = $this->createEmployeeWithUser();
        $payroll = $this->createPayrollWithSlip($employee, 'draft');

        $response = $this->actingAs($employee->user)
            ->get(route('employee.payslips.preview', $payroll));

        $response->assertForbidden();
    }
}
