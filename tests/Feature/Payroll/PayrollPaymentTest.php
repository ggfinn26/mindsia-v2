<?php

namespace Tests\Feature\Payroll;

use App\Models\EmployeePayroll;
use App\Models\EmployeePayrollPayment;
use App\Models\PayrollPeriod;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PayrollPaymentTest extends TestCase
{
    private User $adminUser;

    private int $adminEmployeeId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminEmployeeId = $this->insertEmployee('PY-ADMIN');
        $this->adminUser = User::factory()->create(['employee_id' => $this->adminEmployeeId]);
        $this->adminUser->givePermissionTo([
            'payroll.period.view',
            'payroll.period.pay',
        ]);
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
    // Helper: create a finalized period with an employee payroll
    // ============================================================

    private function createFinalizedPeriodWithPayroll(
        float $netAmount = 5000000,
        string $prefix = 'PY',
    ): array {
        $period = PayrollPeriod::factory()->finalized()->create();

        $employeeId = $this->insertEmployee($prefix.'_'.uniqid());

        $payroll = EmployeePayroll::factory()->forPeriod($period->id)->forEmployee($employeeId)->create([
            'total_earnings' => $netAmount + 500000,
            'total_deductions' => 500000,
            'net_amount' => $netAmount,
            'payment_status' => 'unpaid',
        ]);

        return [
            'period' => $period,
            'employeeId' => $employeeId,
            'payroll' => $payroll,
        ];
    }

    // ============================================================
    // Route & Access — PY-01 to PY-03
    // ============================================================

    /** PY-01: Process payment route exists and responds */
    public function test_py01_payment_route_exists(): void
    {
        $data = $this->createFinalizedPeriodWithPayroll();

        $response = $this->actingAs($this->adminUser)
            ->post(route('payroll.payrolls.payment.store', [$data['period'], $data['payroll']]), [
                'payment_method' => 'bank_transfer',
                'amount' => 5000000,
            ]);

        $response->assertRedirect();
        $this->assertNotEquals(404, $response->getStatusCode());
    }

    /** PY-02: Mark payment failed route exists and responds */
    public function test_py02_mark_failed_route_exists(): void
    {
        $data = $this->createFinalizedPeriodWithPayroll();
        $payment = EmployeePayrollPayment::create([
            'employee_payroll_id' => $data['payroll']->id,
            'payment_status' => 'paid',
            'payment_method' => 'bank_transfer',
            'amount' => 5000000,
            'paid_at' => now(),
        ]);

        $response = $this->actingAs($this->adminUser)
            ->patch(route('payroll.payrolls.payment.failed', [$data['period'], $data['payroll'], $payment]), [
                'failure_reason' => 'Transfer gagal di bank',
            ]);

        $response->assertRedirect();
        $this->assertNotEquals(404, $response->getStatusCode());
    }

    /** PY-03: Process payment without payroll.period.pay permission → 403 */
    public function test_py03_payment_without_permission(): void
    {
        $unauthEmployeeId = $this->insertEmployee('PY-UNAUTH');
        $unauthorized = User::factory()->create(['employee_id' => $unauthEmployeeId]);
        $unauthorized->givePermissionTo(['payroll.period.view']);
        $data = $this->createFinalizedPeriodWithPayroll();

        $response = $this->actingAs($unauthorized)
            ->post(route('payroll.payrolls.payment.store', [$data['period'], $data['payroll']]), [
                'payment_method' => 'bank_transfer',
                'amount' => 5000000,
            ]);

        $response->assertForbidden();
    }

    // ============================================================
    // Status Guard — PY-04 to PY-05
    // ============================================================

    /** PY-04: Process payment when period finalized succeeds */
    public function test_py04_payment_when_finalized_succeeds(): void
    {
        $data = $this->createFinalizedPeriodWithPayroll(netAmount: 5000000);
        $payroll = $data['payroll'];

        $response = $this->actingAs($this->adminUser)
            ->post(route('payroll.payrolls.payment.store', [$data['period'], $payroll]), [
                'payment_method' => 'bank_transfer',
                'amount' => 5000000,
            ]);

        $response->assertRedirect(route('payroll.payments.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('employee_payroll_payments', [
            'employee_payroll_id' => $payroll->id,
            'payment_method' => 'bank_transfer',
            'amount' => 5000000,
            'payment_status' => 'paid',
        ]);

        $payroll->refresh();
        $this->assertEquals('paid', $payroll->payment_status);
    }

    /** PY-05: Process payment when period NOT finalized blocked */
    public function test_py05_payment_when_not_finalized_blocked(): void
    {
        $period = PayrollPeriod::factory()->review()->create();
        $employeeId = $this->insertEmployee('PY-NF');
        $payroll = EmployeePayroll::factory()->forPeriod($period->id)->forEmployee($employeeId)->create();

        $response = $this->actingAs($this->adminUser)
            ->post(route('payroll.payrolls.payment.store', [$period, $payroll]), [
                'payment_method' => 'bank_transfer',
                'amount' => 5000000,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertStringContainsString('finalized', strtolower(session('error')));
    }

    // ============================================================
    // Partial Payment — PY-06 to PY-10
    // ============================================================

    /** PY-06: Partial payment — amount < net_amount → status partial */
    public function test_py06_partial_payment_sets_partial_status(): void
    {
        $data = $this->createFinalizedPeriodWithPayroll(netAmount: 5000000);
        $payroll = $data['payroll'];

        $response = $this->actingAs($this->adminUser)
            ->post(route('payroll.payrolls.payment.store', [$data['period'], $payroll]), [
                'payment_method' => 'bank_transfer',
                'amount' => 2000000,
            ]);

        $response->assertRedirect(route('payroll.payments.index'));

        $this->assertDatabaseHas('employee_payroll_payments', [
            'employee_payroll_id' => $payroll->id,
            'amount' => 2000000,
            'payment_status' => 'paid',
        ]);

        $payroll->refresh();
        $this->assertEquals('partial', $payroll->payment_status);
    }

    /** PY-07: Partial status works correctly after first partial payment */
    public function test_py07_partial_status_works(): void
    {
        $data = $this->createFinalizedPeriodWithPayroll(netAmount: 5000000);
        $payroll = $data['payroll'];

        $this->actingAs($this->adminUser)
            ->post(route('payroll.payrolls.payment.store', [$data['period'], $payroll]), [
                'payment_method' => 'bank_transfer',
                'amount' => 2000000,
            ]);

        $payroll->refresh();
        $this->assertEquals('partial', $payroll->payment_status);

        $totalPaid = $payroll->payments()->where('payment_status', 'paid')->sum('amount');
        $this->assertEquals(2000000, (float) $totalPaid);
        $this->assertGreaterThan(0, (float) $payroll->net_amount - $totalPaid);
    }

    /** PY-08: Full payment — amount = net_amount → status paid */
    public function test_py08_full_payment_status_paid(): void
    {
        $data = $this->createFinalizedPeriodWithPayroll(netAmount: 5000000);
        $payroll = $data['payroll'];

        $this->actingAs($this->adminUser)
            ->post(route('payroll.payrolls.payment.store', [$data['period'], $payroll]), [
                'payment_method' => 'bank_transfer',
                'amount' => 5000000,
            ]);

        $payroll->refresh();
        $this->assertEquals('paid', $payroll->payment_status);
        $this->assertNotNull($payroll->paid_at);
    }

    /** PY-09: Overpayment blocked */
    public function test_py09_overpayment_blocked(): void
    {
        $data = $this->createFinalizedPeriodWithPayroll(netAmount: 5000000);
        $payroll = $data['payroll'];

        $response = $this->actingAs($this->adminUser)
            ->post(route('payroll.payrolls.payment.store', [$data['period'], $payroll]), [
                'payment_method' => 'bank_transfer',
                'amount' => 6000000,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertStringContainsString('melebihi sisa', strtolower(session('error')));
    }

    /** PY-10: Multiple partial payments sum = net_amount → status paid */
    public function test_py10_multiple_partials_sum_to_paid(): void
    {
        $data = $this->createFinalizedPeriodWithPayroll(netAmount: 5000000);
        $payroll = $data['payroll'];

        $this->actingAs($this->adminUser)
            ->post(route('payroll.payrolls.payment.store', [$data['period'], $payroll]), [
                'payment_method' => 'bank_transfer',
                'amount' => 2000000,
            ]);

        $payroll->refresh();
        $this->assertEquals('partial', $payroll->payment_status);

        $this->actingAs($this->adminUser)
            ->post(route('payroll.payrolls.payment.store', [$data['period'], $payroll]), [
                'payment_method' => 'cash',
                'amount' => 3000000,
            ]);

        $payroll->refresh();
        $this->assertEquals('paid', $payroll->payment_status);

        $totalPaid = $payroll->payments()->where('payment_status', 'paid')->sum('amount');
        $this->assertEquals(5000000, (float) $totalPaid);
    }

    // ============================================================
    // Mark Failed — PY-11 to PY-13
    // ============================================================

    /** PY-11: Mark payment failed — route now has {payment} param */
    public function test_py11_mark_failed_works(): void
    {
        $data = $this->createFinalizedPeriodWithPayroll();
        $payment = EmployeePayrollPayment::create([
            'employee_payroll_id' => $data['payroll']->id,
            'payment_status' => 'paid',
            'payment_method' => 'bank_transfer',
            'amount' => 5000000,
            'paid_at' => now(),
        ]);

        $response = $this->actingAs($this->adminUser)
            ->patch(route('payroll.payrolls.payment.failed', [$data['period'], $data['payroll'], $payment]), [
                'failure_reason' => 'Transfer gagal di bank',
            ]);

        $response->assertRedirect(route('payroll.payments.index'));

        $payment->refresh();
        $this->assertEquals('failed', $payment->payment_status,
            'markFailed should update payment status to failed');
    }

    /** PY-12: Mark failed without permission → 403 */
    public function test_py12_mark_failed_without_permission(): void
    {
        $unauthEmployeeId = $this->insertEmployee('PY-MF-UNAUTH');
        $unauthorized = User::factory()->create(['employee_id' => $unauthEmployeeId]);
        $data = $this->createFinalizedPeriodWithPayroll();
        $payment = EmployeePayrollPayment::create([
            'employee_payroll_id' => $data['payroll']->id,
            'payment_status' => 'paid',
            'payment_method' => 'bank_transfer',
            'amount' => 5000000,
            'paid_at' => now(),
        ]);

        $response = $this->actingAs($unauthorized)
            ->patch(route('payroll.payrolls.payment.failed', [$data['period'], $data['payroll'], $payment]), [
                'failure_reason' => 'Should not work',
            ]);

        $response->assertForbidden();
    }

    /** PY-13: Mark failed — failure_reason required */
    public function test_py13_mark_failed_reason_required(): void
    {
        $data = $this->createFinalizedPeriodWithPayroll();
        $payment = EmployeePayrollPayment::create([
            'employee_payroll_id' => $data['payroll']->id,
            'payment_status' => 'paid',
            'payment_method' => 'bank_transfer',
            'amount' => 5000000,
            'paid_at' => now(),
        ]);

        $response = $this->actingAs($this->adminUser)
            ->patch(route('payroll.payrolls.payment.failed', [$data['period'], $data['payroll'], $payment]), [
                'failure_reason' => '',
            ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('failure_reason');
    }

    // ============================================================
    // Security — PY-14 to PY-15
    // ============================================================

    /** PY-14: paid_by_employee_id null-safe — user without employee record */
    public function test_py14_paid_by_employee_id_null_safe(): void
    {
        $data = $this->createFinalizedPeriodWithPayroll(netAmount: 5000000);
        $payroll = $data['payroll'];

        $userWithoutEmployee = User::factory()->create(['employee_id' => null]);
        $userWithoutEmployee->givePermissionTo(['payroll.period.pay']);

        $response = $this->actingAs($userWithoutEmployee)
            ->post(route('payroll.payrolls.payment.store', [$data['period'], $payroll]), [
                'payment_method' => 'bank_transfer',
                'amount' => 5000000,
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('employee_payroll_payments', [
            'employee_payroll_id' => $payroll->id,
            'paid_by_employee_id' => null,
            'amount' => 5000000,
        ]);
    }

    /** PY-15: payment_method validation — only bank_transfer and cash allowed */
    public function test_py15_payment_method_other_rejected(): void
    {
        $data = $this->createFinalizedPeriodWithPayroll();
        $payroll = $data['payroll'];

        $response = $this->actingAs($this->adminUser)
            ->post(route('payroll.payrolls.payment.store', [$data['period'], $payroll]), [
                'payment_method' => 'other',
                'amount' => 5000000,
            ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('payment_method');
    }

    // ============================================================
    // Supplementary — validation edge cases
    // ============================================================

    /** PY-20: amount empty → validation error */
    public function test_py20_amount_empty_validation(): void
    {
        $data = $this->createFinalizedPeriodWithPayroll();

        $response = $this->actingAs($this->adminUser)
            ->post(route('payroll.payrolls.payment.store', [$data['period'], $data['payroll']]), [
                'payment_method' => 'bank_transfer',
                'amount' => null,
            ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('amount');
    }

    /** PY-21: amount negative → validation error */
    public function test_py21_amount_negative_validation(): void
    {
        $data = $this->createFinalizedPeriodWithPayroll();

        $response = $this->actingAs($this->adminUser)
            ->post(route('payroll.payrolls.payment.store', [$data['period'], $data['payroll']]), [
                'payment_method' => 'bank_transfer',
                'amount' => -1,
            ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('amount');
    }

    /** PY-22: amount=0 → validation error (min:0.01) */
    public function test_py22_amount_zero_validation(): void
    {
        $data = $this->createFinalizedPeriodWithPayroll();

        $response = $this->actingAs($this->adminUser)
            ->post(route('payroll.payrolls.payment.store', [$data['period'], $data['payroll']]), [
                'payment_method' => 'bank_transfer',
                'amount' => 0,
            ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('amount');
    }

    /** PY-23: amount exceeds net_amount → overpayment blocked */
    public function test_py23_amount_exceeds_net_amount(): void
    {
        $data = $this->createFinalizedPeriodWithPayroll(netAmount: 5000000);

        $response = $this->actingAs($this->adminUser)
            ->post(route('payroll.payrolls.payment.store', [$data['period'], $data['payroll']]), [
                'payment_method' => 'bank_transfer',
                'amount' => 5000001,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /** PY-24: payment_method invalid → validation error */
    public function test_py24_payment_method_invalid(): void
    {
        $data = $this->createFinalizedPeriodWithPayroll();

        $response = $this->actingAs($this->adminUser)
            ->post(route('payroll.payrolls.payment.store', [$data['period'], $data['payroll']]), [
                'payment_method' => 'crypto',
                'amount' => 5000000,
            ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('payment_method');
    }

    /** PY-25: Payment index page accessible */
    public function test_py25_payment_index_accessible(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('payroll.payments.index'));

        $response->assertOk();
    }

    /** PY-26: Payment index without permission → 403 */
    public function test_py26_payment_index_without_permission(): void
    {
        $unauthEmployeeId = $this->insertEmployee('PY-IDX-UNAUTH');
        $unauthorized = User::factory()->create(['employee_id' => $unauthEmployeeId]);

        $response = $this->actingAs($unauthorized)
            ->get(route('payroll.payments.index'));

        $response->assertForbidden();
    }
}
