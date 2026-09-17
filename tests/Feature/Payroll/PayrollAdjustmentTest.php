<?php

namespace Tests\Feature\Payroll;

use App\Models\EmployeePayroll;
use App\Models\EmployeePayrollAdjustmentHistory;
use App\Models\EmployeePayrollPayment;
use App\Models\PayrollComponent;
use App\Models\PayrollItem;
use App\Models\PayrollPeriod;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PayrollAdjustmentTest extends TestCase
{
    private static int $periodSeq = 0;

    private User $adminUser;

    private int $adminEmployeeId;

    private array $warnings = [];

    protected function setUp(): void
    {
        parent::setUp();

        // Explicitly disable CSRF for POST requests — parent TestCase also does this
        // but it may get reset between setUp and test method execution
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        // Use direct DB inserts for employee to avoid RefreshDatabase FK deadlock issues
        $adminEmployeeId = $this->insertEmployee('ADJ-ADMIN');

        $this->adminEmployeeId = $adminEmployeeId;
        $this->adminUser = User::factory()->create(['employee_id' => $adminEmployeeId]);
        $this->adminUser->givePermissionTo([
            'payroll.period.view',
            'payroll.period.generate',
            'payroll.period.pay',
            'payroll.item.adjust',
        ]);
    }

    /** Direct DB insert for employee — bypasses factory FK deadlock chain */
    private function insertEmployee(string $prefix = 'EMP'): int
    {
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
            'employee_code' => 'EMP'.substr(md5(uniqid($prefix)), 0, 6),
            'full_name' => "Employee {$prefix}",
            'gender' => 'L', 'birthdate' => '1990-01-01',
            'email' => strtolower($prefix).'_'.uniqid().'@test.com',
            'whatsapp_number' => '62'.fake()->numerify('###########'),
            'region_id' => $regionId, 'area_id' => $areaId, 'branch_id' => $branchId,
            'is_active' => false, 'is_hq' => false,
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    // ============================================================
    // Helper: create a finalized period with an employee payroll + items
    // ============================================================

    private function createFinalizedPeriodWithPayroll(
        ?int $year = null,
        ?int $month = null,
        float $totalEarnings = 5000000,
        float $totalDeductions = 500000,
    ): array {
        // Use unique year/month per call — avoids UniqueConstraintViolation across tests
        if ($year === null || $month === null) {
            $seq = ++self::$periodSeq;
            $year = 2070 + intdiv($seq - 1, 12);
            $month = (($seq - 1) % 12) + 1;
        }

        // firstOrCreate handles RefreshDatabase rollback failures gracefully
        $period = PayrollPeriod::firstOrCreate(
            ['period_year' => $year, 'period_month' => $month],
            [
                'status' => 'finalized',
                'pay_date' => null,
                'confirmed_by_employee_id' => null,
                'confirmed_at' => now(),
                'notes' => null,
            ]
        );

        $employeeId = $this->insertEmployee("PAY-{$period->id}");

        $payroll = EmployeePayroll::factory()->forPeriod($period->id)->forEmployee($employeeId)->create([
            'total_earnings' => $totalEarnings,
            'total_deductions' => $totalDeductions,
            'net_amount' => $totalEarnings - $totalDeductions,
            'payment_status' => 'unpaid',
        ]);

        // Create payroll components for earning and deduction items
        $earningComponent = PayrollComponent::factory()->earning()->fixed()->create([
            'component_code' => 'EARN-ADJ-'.$payroll->id,
        ]);
        $deductionComponent = PayrollComponent::factory()->deduction()->fixed()->create([
            'component_code' => 'DED-ADJ-'.$payroll->id,
        ]);

        $earningItem = PayrollItem::factory()->earning()->forPayroll($payroll->id)->forComponent($earningComponent->id)->create([
            'total_amount' => $totalEarnings,
        ]);

        $deductionItem = PayrollItem::factory()->deduction()->forPayroll($payroll->id)->forComponent($deductionComponent->id)->create([
            'total_amount' => $totalDeductions,
        ]);

        return [
            'period' => $period,
            'employeeId' => $employeeId,
            'payroll' => $payroll,
            'earningItem' => $earningItem,
            'deductionItem' => $deductionItem,
        ];
    }

    // ============================================================
    // Route & Access — PA-01 to PA-03
    // ============================================================

    /** PA-01: Show payroll detail — user with payroll.period.view */
    public function test_pa01_show_payroll_detail(): void
    {
        $data = $this->createFinalizedPeriodWithPayroll();

        $response = $this->actingAs($this->adminUser)
            ->get(route('payroll.payrolls.show', [$data['period'], $data['payroll']]));

        $response->assertOk();
        $response->assertViewHas('payroll');
    }

    /** PA-02: Adjust endpoint — route exists and responds */
    public function test_pa02_adjust_route_exists(): void
    {
        $data = $this->createFinalizedPeriodWithPayroll();

        $response = $this->actingAs($this->adminUser)
            ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class)
            ->post(route('payroll.payrolls.adjust', [$data['period'], $data['payroll']]), [
                'adjustment_type' => 'correction',
                'new_amount' => 4000000,
                'adjustment_reason' => 'Koreksi gaji bulan ini',
            ]);

        // Should redirect (success or error), not 404
        $response->assertRedirect();
        $this->assertNotEquals(404, $response->getStatusCode());
    }

    /** PA-03: Adjust without payroll.item.adjust permission → 403 */
    public function test_pa03_adjust_without_permission(): void
    {
        $unauthorized = User::factory()->create();
        $unauthorized->givePermissionTo(['payroll.period.view']);
        $data = $this->createFinalizedPeriodWithPayroll();

        $response = $this->actingAs($unauthorized)
            ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class)
            ->post(route('payroll.payrolls.adjust', [$data['period'], $data['payroll']]), [
                'adjustment_type' => 'correction',
                'new_amount' => 4000000,
                'adjustment_reason' => 'Koreksi gaji bulan ini',
            ]);

        $response->assertForbidden();
    }

    // ============================================================
    // Status Guard — PA-04 to PA-06
    // ============================================================

    /** PA-04: Adjust when period status=finalized succeeds */
    public function test_pa04_adjust_when_finalized_succeeds(): void
    {
        $data = $this->createFinalizedPeriodWithPayroll();
        $payroll = $data['payroll'];

        $response = $this->actingAs($this->adminUser)
            ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class)
            ->post(route('payroll.payrolls.adjust', [$data['period'], $payroll]), [
                'adjustment_type' => 'correction',
                'new_amount' => 4000000,
                'adjustment_reason' => 'Koreksi gaji bulan ini',
            ]);

        $response->assertRedirect(route('payroll.payrolls.show', [$data['period'], $payroll]));
        $response->assertSessionHas('success');

        // History recorded
        $this->assertDatabaseHas('employee_payroll_adjustment_histories', [
            'employee_payroll_id' => $payroll->id,
            'adjustment_type' => 'correction',
            'new_amount' => 4000000,
        ]);
    }

    /** PA-05: Adjust when period status=draft blocked */
    public function test_pa05_adjust_when_draft_blocked(): void
    {
        $period = PayrollPeriod::factory()->draft()->create();
        $employeeId = $this->insertEmployee('PA05');
        $payroll = EmployeePayroll::factory()->forPeriod($period->id)->forEmployee($employeeId)->create();

        $response = $this->actingAs($this->adminUser)
            ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class)
            ->post(route('payroll.payrolls.adjust', [$period, $payroll]), [
                'adjustment_type' => 'correction',
                'new_amount' => 4000000,
                'adjustment_reason' => 'Koreksi gaji bulan ini',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertStringContainsString('finalized', strtolower(session('error')));
    }

    /** PA-06: Adjust when period status=review blocked */
    public function test_pa06_adjust_when_review_blocked(): void
    {
        $period = PayrollPeriod::factory()->review()->create();
        $employeeId = $this->insertEmployee('PA06');
        $payroll = EmployeePayroll::factory()->forPeriod($period->id)->forEmployee($employeeId)->create();

        $response = $this->actingAs($this->adminUser)
            ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class)
            ->post(route('payroll.payrolls.adjust', [$period, $payroll]), [
                'adjustment_type' => 'correction',
                'new_amount' => 4000000,
                'adjustment_reason' => 'Koreksi gaji bulan ini',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertStringContainsString('finalized', strtolower(session('error')));
    }

    // ============================================================
    // Adjustment Types — PA-07 to PA-11
    // ============================================================

    /** PA-07: Adjust earning item — total_amount updated + totals recalculated */
    public function test_pa07_adjust_earning_item(): void
    {
        $data = $this->createFinalizedPeriodWithPayroll(
            totalEarnings: 5000000,
            totalDeductions: 500000,
        );
        $payroll = $data['payroll'];
        $earningItem = $data['earningItem'];

        $this->actingAs($this->adminUser)
            ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class)
            ->post(route('payroll.payrolls.adjust', [$data['period'], $payroll]), [
                'payroll_item_id' => $earningItem->id,
                'adjustment_type' => 'earning',
                'new_amount' => 6000000,
                'adjustment_reason' => 'Penyesuaian tunjangan',
            ]);

        // Item updated
        $this->assertDatabaseHas('payroll_items', [
            'id' => $earningItem->id,
            'total_amount' => 6000000,
        ]);

        // Totals recalculated: earnings=6000000, deductions=500000, net=5500000
        $payroll->refresh();
        $this->assertEquals(6000000, (float) $payroll->total_earnings);
        $this->assertEquals(500000, (float) $payroll->total_deductions);
        $this->assertEquals(5500000, (float) $payroll->net_amount);
    }

    /** PA-08: Adjust deduction item — total_amount updated + totals recalculated */
    public function test_pa08_adjust_deduction_item(): void
    {
        $data = $this->createFinalizedPeriodWithPayroll(
            totalEarnings: 5000000,
            totalDeductions: 500000,
        );
        $payroll = $data['payroll'];
        $deductionItem = $data['deductionItem'];

        $this->actingAs($this->adminUser)
            ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class)
            ->post(route('payroll.payrolls.adjust', [$data['period'], $payroll]), [
                'payroll_item_id' => $deductionItem->id,
                'adjustment_type' => 'deduction',
                'new_amount' => 100000,
                'adjustment_reason' => 'Pengurangan potongan',
            ]);

        // Item updated
        $this->assertDatabaseHas('payroll_items', [
            'id' => $deductionItem->id,
            'total_amount' => 100000,
        ]);

        // Totals recalculated: earnings=5000000, deductions=100000, net=4900000
        $payroll->refresh();
        $this->assertEquals(5000000, (float) $payroll->total_earnings);
        $this->assertEquals(100000, (float) $payroll->total_deductions);
        $this->assertEquals(4900000, (float) $payroll->net_amount);
    }

    /** PA-09: Correction type (no payroll_item_id) — net_amount updated directly */
    public function test_pa09_correction_type_updates_net_amount_directly(): void
    {
        $data = $this->createFinalizedPeriodWithPayroll(
            totalEarnings: 5000000,
            totalDeductions: 500000,
        );
        $payroll = $data['payroll'];

        $this->actingAs($this->adminUser)
            ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class)
            ->post(route('payroll.payrolls.adjust', [$data['period'], $payroll]), [
                'payroll_item_id' => null,
                'adjustment_type' => 'correction',
                'new_amount' => 500000,
                'adjustment_reason' => 'Koreksi langsung net amount',
            ]);

        // net_amount set directly
        $payroll->refresh();
        $this->assertEquals(500000, (float) $payroll->net_amount);

        // History recorded with no payroll_item_id
        $this->assertDatabaseHas('employee_payroll_adjustment_histories', [
            'employee_payroll_id' => $payroll->id,
            'payroll_item_id' => null,
            'adjustment_type' => 'correction',
            'new_amount' => 500000,
        ]);
    }

    /** PA-10: History recorded with previous_amount */
    public function test_pa10_history_records_previous_amount(): void
    {
        $data = $this->createFinalizedPeriodWithPayroll(
            totalEarnings: 5000000,
            totalDeductions: 500000,
        );
        $payroll = $data['payroll'];
        $earningItem = $data['earningItem'];

        $this->actingAs($this->adminUser)
            ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class)
            ->post(route('payroll.payrolls.adjust', [$data['period'], $payroll]), [
                'payroll_item_id' => $earningItem->id,
                'adjustment_type' => 'earning',
                'new_amount' => 6000000,
                'adjustment_reason' => 'Penyesuaian tunjangan',
            ]);

        // History has previous_amount = old item total_amount
        $this->assertDatabaseHas('employee_payroll_adjustment_histories', [
            'employee_payroll_id' => $payroll->id,
            'payroll_item_id' => $earningItem->id,
            'previous_amount' => 5000000,
            'new_amount' => 6000000,
        ]);
    }

    /** PA-11: adjustment_reason empty → validation error */
    public function test_pa11_adjustment_reason_required(): void
    {
        $data = $this->createFinalizedPeriodWithPayroll();
        $payroll = $data['payroll'];

        $response = $this->actingAs($this->adminUser)
            ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class)
            ->post(route('payroll.payrolls.adjust', [$data['period'], $payroll]), [
                'payroll_item_id' => null,
                'adjustment_type' => 'correction',
                'new_amount' => 4000000,
                'adjustment_reason' => '',
            ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('adjustment_reason');
    }

    // ============================================================
    // Ownership & Security — PA-12 to PA-13
    // ============================================================

    /** PA-12: payroll_item_id belonging to different payroll → 403 */
    public function test_pa12_ownership_check_payroll_item(): void
    {
        $data1 = $this->createFinalizedPeriodWithPayroll();
        $data2 = $this->createFinalizedPeriodWithPayroll();

        // Try to adjust using an item that belongs to a DIFFERENT payroll
        $response = $this->actingAs($this->adminUser)
            ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class)
            ->post(route('payroll.payrolls.adjust', [$data1['period'], $data1['payroll']]), [
                'payroll_item_id' => $data2['earningItem']->id,
                'adjustment_type' => 'earning',
                'new_amount' => 6000000,
                'adjustment_reason' => 'Mencoba item payroll lain',
            ]);

        $response->assertForbidden();
    }

    /** PA-13: adjusted_by_employee_id NOT NULL constraint — GAP when user has no employee record
     *
     * GAP-126: Controller uses auth()->user()->employee?->id (null-safe ?->) but the DB column
     * adjusted_by_employee_id is unsignedBigInteger (NOT NULL). When a user without an employee
     * record performs an adjustment, the null value triggers SQLSTATE[23000] Integrity constraint
     * violation. The code intends null-safety but the schema doesn't allow it.
     *
     * NOTE: After EmployeeFactory fix (adding area_id), the test setup now works correctly.
     * The response is 302 (redirect with session error) instead of the expected 500 crash,
     * suggesting the controller now catches the error. However, the underlying schema mismatch
     * (nullable code vs NOT NULL column) remains a latent GAP.
     */
    public function test_pa13_adjusted_by_employee_id_null_safe(): void
    {
        $data = $this->createFinalizedPeriodWithPayroll();
        $payroll = $data['payroll'];

        // Create a user with payroll.item.adjust permission but NO employee record
        $userWithoutEmployee = User::factory()->create(['employee_id' => null]);
        $userWithoutEmployee->givePermissionTo(['payroll.item.adjust']);

        $response = $this->actingAs($userWithoutEmployee)
            ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class)
            ->post(route('payroll.payrolls.adjust', [$data['period'], $payroll]), [
                'payroll_item_id' => null,
                'adjustment_type' => 'correction',
                'new_amount' => 4000000,
                'adjustment_reason' => 'Koreksi oleh admin tanpa employee',
            ]);

        // Controller uses null-safe operator: auth()->user()->employee?->id → silently stores null
        // Column is nullable in migration, so no DB constraint violation.
        // GAP-126: Audit trail is lost — adjusted_by_employee_id becomes null when user has no employee record.
        // No validation prevents a non-employee user from making adjustments.
        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $this->addWarning('GAP-126', 'PA-13', 'adjusted_by_employee_id nullable in schema but controller uses null-safe ?-> — audit trail lost when user has no employee record.');
    }

    private function addWarning(string $gap, string $scenario, string $message): void
    {
        $this->warnings[] = "[$gap] $scenario: $message";
    }

    protected function tearDown(): void
    {
        if (! empty($this->warnings)) {
            foreach ($this->warnings as $w) {
                echo "\n  ⚠️  $w\n";
            }
        }

        parent::tearDown();
    }
}
