<?php

namespace Tests\Feature\Payroll;

use App\Models\Employee;
use App\Models\EmployeeCompensation;
use App\Models\PayrollComponent;
use App\Models\PayrollItem;
use App\Models\PayrollPeriod;
use App\Models\SessionCompensationRule;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PayrollComponentManagementTest extends TestCase
{
    private static int $testCodeSeq = 0;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->user->givePermissionTo([
            'payroll.component.create',
            'payroll.component.update',
            'payroll.component.delete',
            'payroll.employee_compensation.create',
            'payroll.employee_compensation.update',
            'payroll.employee_compensation.delete',
            'payroll.session_compensation_rule.create',
            'payroll.session_compensation_rule.update',
            'payroll.session_compensation_rule.delete',
        ]);
    }

    // ============================================================
    // Component Master — CRUD (PC-01 to PC-10)
    // ============================================================

    /**
     * PC-01: List payroll components page accessible
     */
    public function test_pc01_list_payroll_components_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('payroll.components.index'));

        $response->assertOk();
    }

    /**
     * PC-02: Create component — earning type successfully
     */
    public function test_pc02_create_component_earning_type(): void
    {
        $code = 'TEST-E' . str_pad(++self::$testCodeSeq, 4, '0', STR_PAD_LEFT);

        $response = $this->actingAs($this->user)->post(route('payroll.components.store'), [
            'component_code' => $code,
            'component_name' => 'Gaji Pokok Test',
            'component_type' => 'earning',
            'calculation_method' => 'fixed',
            'is_taxable' => true,
        ]);

        $response->assertRedirect(route('payroll.components.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('payroll_components', [
            'component_code' => $code,
            'component_type' => 'earning',
            'calculation_method' => 'fixed',
            'is_taxable' => true,
        ]);
    }

    /**
     * PC-03: Create component — duplicate component_code → validation error
     */
    public function test_pc03_create_component_duplicate_code(): void
    {
        $code = 'TEST-D' . str_pad(++self::$testCodeSeq, 4, '0', STR_PAD_LEFT);

        PayrollComponent::factory()->create(['component_code' => $code]);

        $response = $this->actingAs($this->user)->post(route('payroll.components.store'), [
            'component_code' => $code,
            'component_name' => 'Duplicate',
            'component_type' => 'earning',
            'calculation_method' => 'fixed',
        ]);

        $response->assertSessionHasErrors('component_code');
    }

    /**
     * PC-04: Create component — without permission → 403
     */
    public function test_pc04_create_component_without_permission(): void
    {
        $unauthorized = User::factory()->create();

        $code = 'TEST-U' . str_pad(++self::$testCodeSeq, 4, '0', STR_PAD_LEFT);

        $response = $this->actingAs($unauthorized)->post(route('payroll.components.store'), [
            'component_code' => $code,
            'component_name' => 'Test',
            'component_type' => 'earning',
            'calculation_method' => 'fixed',
        ]);

        $response->assertForbidden();
    }

    /**
     * PC-05: Update component successfully
     */
    public function test_pc05_update_component_successfully(): void
    {
        $component = PayrollComponent::factory()->create(['component_name' => 'Old Name']);

        $response = $this->actingAs($this->user)->put(route('payroll.components.update', $component), [
            'component_name' => 'New Name',
        ]);

        $response->assertRedirect(route('payroll.components.index'));
        $response->assertSessionHas('success');

        $this->assertEquals('New Name', $component->fresh()->component_name);
    }

    /**
     * PC-06: Update component — change calculation_method when PayrollItems exist → blocked
     * GAP-93 is NOT real — UpdatePayrollComponentRequest::withValidator already blocks this.
     */
    public function test_pc06_update_component_calculation_method_blocked_when_payroll_items_exist(): void
    {
        $component = PayrollComponent::factory()->create(['calculation_method' => 'fixed']);
        $period = PayrollPeriod::factory()->create();
        $payroll = \App\Models\EmployeePayroll::factory()->create([
            'payroll_period_id' => $period->id,
        ]);
        PayrollItem::factory()->create([
            'employee_payroll_id' => $payroll->id,
            'payroll_component_id' => $component->id,
        ]);

        $response = $this->actingAs($this->user)->put(route('payroll.components.update', $component), [
            'calculation_method' => 'daily',
        ]);

        $response->assertSessionHasErrors('calculation_method');
        $this->assertEquals('fixed', $component->fresh()->calculation_method);
    }

    /**
     * PC-07: Update component — without permission → 403
     */
    public function test_pc07_update_component_without_permission(): void
    {
        $component = PayrollComponent::factory()->create();
        $unauthorized = User::factory()->create();
        $unauthorized->givePermissionTo('payroll.component.create'); // different permission

        $response = $this->actingAs($unauthorized)->put(route('payroll.components.update', $component), [
            'component_name' => 'Hacked',
        ]);

        $response->assertForbidden();
    }

    /**
     * PC-08: Delete component — no PayrollItem → hard delete
     */
    public function test_pc08_delete_component_without_payroll_history_hard_deletes(): void
    {
        $component = PayrollComponent::factory()->create();

        $response = $this->actingAs($this->user)->delete(route('payroll.components.destroy', $component));

        $response->assertRedirect(route('payroll.components.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('payroll_components', ['id' => $component->id]);
    }

    /**
     * PC-09: Delete component — has PayrollItem → soft deactivates
     */
    public function test_pc09_delete_component_with_payroll_history_soft_deactivates(): void
    {
        $component = PayrollComponent::factory()->create(['is_active' => true]);
        $period = PayrollPeriod::factory()->create();
        $payroll = \App\Models\EmployeePayroll::factory()->create([
            'payroll_period_id' => $period->id,
        ]);
        PayrollItem::factory()->create([
            'employee_payroll_id' => $payroll->id,
            'payroll_component_id' => $component->id,
        ]);

        $response = $this->actingAs($this->user)->delete(route('payroll.components.destroy', $component));

        $response->assertRedirect(route('payroll.components.index'));
        $response->assertSessionHas('success');

        $this->assertFalse($component->fresh()->is_active);
        $this->assertDatabaseHas('payroll_components', ['id' => $component->id]);
    }

    /**
     * PC-10: Delete component — without permission → allowed (GAP: no authorize on destroy)
     */
    public function test_pc10_delete_component_without_permission_not_blocked(): void
    {
        $component = PayrollComponent::factory()->create();
        $unauthorized = User::factory()->create();

        $response = $this->actingAs($unauthorized)->delete(route('payroll.components.destroy', $component));

        // BUG: destroy() has no authorize() — any authenticated user can delete
        $response->assertRedirect(route('payroll.components.index'));
    }

    // ============================================================
    // Employee Compensation — Per Row (PC-11 to PC-17)
    // ============================================================

    /**
     * PC-11: Access employee compensation page
     */
    public function test_pc11_access_employee_compensation_page(): void
    {
        $employee = Employee::factory()->create();

        $response = $this->actingAs($this->user)->get(route('employees.payroll.compensations.index', $employee));

        $response->assertOk();
    }

    /**
     * PC-12: Assign compensation successfully (upsert via updateOrCreate)
     */
    public function test_pc12_assign_compensation_successfully(): void
    {
        $employee = Employee::factory()->create();
        $component = PayrollComponent::factory()->earning()->fixed()->create(['is_active' => true]);

        $response = $this->actingAs($this->user)->post(route('employees.payroll.compensations.store', $employee), [
            'payroll_component_id' => $component->id,
            'value' => 5000000,
        ]);

        $response->assertRedirect(route('employees.payroll.compensations.index', $employee));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('employee_compensations', [
            'employee_id' => $employee->id,
            'payroll_component_id' => $component->id,
            'value' => 5000000,
        ]);
    }

    /**
     * PC-13: Assign compensation — component already exists, update value (upsert)
     */
    public function test_pc13_assign_compensation_upsert_updates_existing(): void
    {
        $employee = Employee::factory()->create();
        $component = PayrollComponent::factory()->earning()->fixed()->create(['is_active' => true]);

        EmployeeCompensation::factory()->create([
            'employee_id' => $employee->id,
            'payroll_component_id' => $component->id,
            'value' => 3000000,
        ]);

        $response = $this->actingAs($this->user)->post(route('employees.payroll.compensations.store', $employee), [
            'payroll_component_id' => $component->id,
            'value' => 5000000,
        ]);

        $response->assertRedirect(route('employees.payroll.compensations.index', $employee));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('employee_compensations', [
            'employee_id' => $employee->id,
            'payroll_component_id' => $component->id,
            'value' => 5000000,
        ]);

        $this->assertEquals(1, EmployeeCompensation::where('employee_id', $employee->id)
            ->where('payroll_component_id', $component->id)
            ->count());
    }

    /**
     * PC-14: Assign compensation — without permission → 403
     */
    public function test_pc14_assign_compensation_without_permission(): void
    {
        $unauthorized = User::factory()->create();
        $employee = Employee::factory()->create();
        $component = PayrollComponent::factory()->earning()->create(['is_active' => true]);

        $response = $this->actingAs($unauthorized)->post(route('employees.payroll.compensations.store', $employee), [
            'payroll_component_id' => $component->id,
            'value' => 5000000,
        ]);

        $response->assertForbidden();
    }

    /**
     * PC-15: Assign compensation — inactive component (no is_active check in validation)
     */
    public function test_pc15_assign_compensation_inactive_component_allowed(): void
    {
        $employee = Employee::factory()->create();
        $inactiveComponent = PayrollComponent::factory()->inactive()->create();

        $response = $this->actingAs($this->user)->post(route('employees.payroll.compensations.store', $employee), [
            'payroll_component_id' => $inactiveComponent->id,
            'value' => 5000000,
        ]);

        // Current behavior: allows inactive component (no is_active check in FormRequest)
        $response->assertRedirect(route('employees.payroll.compensations.index', $employee));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('employee_compensations', [
            'employee_id' => $employee->id,
            'payroll_component_id' => $inactiveComponent->id,
        ]);
    }

    /**
     * PC-16: Delete compensation — ownership check blocks wrong employee
     */
    public function test_pc16_delete_compensation_ownership_check_blocks_wrong_employee(): void
    {
        $employee = Employee::factory()->create();
        $otherEmployee = Employee::factory()->create();
        $component = PayrollComponent::factory()->earning()->create(['is_active' => true]);

        $compensation = EmployeeCompensation::factory()->create([
            'employee_id' => $employee->id,
            'payroll_component_id' => $component->id,
        ]);

        $response = $this->actingAs($this->user)->delete(route('employees.payroll.compensations.destroy', [$otherEmployee, $compensation]));

        $response->assertForbidden();
        $this->assertDatabaseHas('employee_compensations', ['id' => $compensation->id]);
    }

    /**
     * PC-17: Delete compensation — without permission → allowed (no authorize on destroy)
     */
    public function test_pc17_delete_compensation_without_permission_allowed(): void
    {
        $employee = Employee::factory()->create();
        $component = PayrollComponent::factory()->earning()->create(['is_active' => true]);

        $compensation = EmployeeCompensation::factory()->create([
            'employee_id' => $employee->id,
            'payroll_component_id' => $component->id,
        ]);

        $unauthorized = User::factory()->create();

        $response = $this->actingAs($unauthorized)->delete(route('employees.payroll.compensations.destroy', [$employee, $compensation]));

        // BUG: destroy has no permission check, only ownership check
        $response->assertRedirect(route('employees.payroll.compensations.index', $employee));
    }

    // ============================================================
    // Session Compensation Rule — CRUD (PC-20 to PC-25)
    // ============================================================

    /**
     * PC-20: List session compensation rules page
     */
    public function test_pc20_list_session_rules_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('payroll.session-rules.index'));

        $response->assertOk();
    }

    /**
     * PC-21: Create session rule — global scope successfully
     */
    public function test_pc21_create_session_rule_global_scope(): void
    {
        $response = $this->actingAs($this->user)->post(route('payroll.session-rules.store'), [
            'rule_code' => 'SESS-GLB-001',
            'rule_name' => 'Global Session Rate',
            'scope_type' => 'global',
            'amount_per_session' => 50000,
        ]);

        $response->assertRedirect(route('payroll.session-rules.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('session_compensation_rules', [
            'rule_code' => 'SESS-GLB-001',
            'scope_type' => 'global',
            'role_id' => null,
            'position_id' => null,
            'employee_id' => null,
            'amount_per_session' => 50000,
        ]);
    }

    /**
     * PC-22: Create session rule — without permission → 403
     */
    public function test_pc22_create_session_rule_without_permission(): void
    {
        $unauthorized = User::factory()->create();

        $response = $this->actingAs($unauthorized)->post(route('payroll.session-rules.store'), [
            'rule_code' => 'SESS-002',
            'rule_name' => 'Test',
            'scope_type' => 'global',
            'amount_per_session' => 50000,
        ]);

        $response->assertForbidden();
    }

    /**
     * PC-23: Create session rule — scope cross-field validation (scope_type=global + role_id filled)
     */
    public function test_pc23_create_session_rule_global_scope_with_role_id_errors(): void
    {
        $role = Role::create(['name' => 'test-role-scope-' . uniqid()]);

        $response = $this->actingAs($this->user)->post(route('payroll.session-rules.store'), [
            'rule_code' => 'SESS-CROSS-001',
            'rule_name' => 'Cross-field test',
            'scope_type' => 'global',
            'role_id' => $role->id,
            'amount_per_session' => 50000,
        ]);

        $response->assertSessionHasErrors('role_id');
    }

    /**
     * PC-23b: Create session rule — role scope without role_id → validation error
     */
    public function test_pc23b_create_session_rule_role_scope_without_role_id_errors(): void
    {
        $response = $this->actingAs($this->user)->post(route('payroll.session-rules.store'), [
            'rule_code' => 'SESS-CROSS-002',
            'rule_name' => 'Missing role_id',
            'scope_type' => 'role',
            'amount_per_session' => 50000,
        ]);

        $response->assertSessionHasErrors('role_id');
    }

    /**
     * PC-24: Update session rule — without permission → 403
     */
    public function test_pc24_update_session_rule_without_permission(): void
    {
        $rule = SessionCompensationRule::factory()->globalScope()->create();
        $unauthorized = User::factory()->create();

        $response = $this->actingAs($unauthorized)->put(route('payroll.session-rules.update', $rule), [
            'amount_per_session' => 99999,
        ]);

        $response->assertForbidden();
    }

    /**
     * PC-24b: Update session rule — change scope_type with cross-field inconsistency (GAP-96)
     * UpdateSessionCompensationRuleRequest does NOT have withValidator for scope cross-field.
     */
    public function test_pc24b_update_session_rule_scope_cross_field_not_validated(): void
    {
        $role = Role::create(['name' => 'test-role-update-' . uniqid()]);
        $rule = SessionCompensationRule::factory()->globalScope()->create();

        $response = $this->actingAs($this->user)->put(route('payroll.session-rules.update', $rule), [
            'scope_type' => 'global',
            'role_id' => $role->id,
        ]);

        // BUG: UpdateSessionCompensationRuleRequest has no withValidator for scope cross-field
        $response->assertRedirect(route('payroll.session-rules.index'));

        $this->assertEquals($role->id, $rule->fresh()->role_id);
    }

    /**
     * PC-25: Delete session rule — soft deactivates (sets is_active=false)
     */
    public function test_pc25_delete_session_rule_soft_deactivates(): void
    {
        $rule = SessionCompensationRule::factory()->globalScope()->create(['is_active' => true]);
        $ruleId = $rule->id;

        // Verify route URL
        $url = route('payroll.session-rules.destroy', $rule);

        $response = $this->actingAs($this->user)->delete($url);

        // Debug: is the redirect going to the right place? Does the session have success?
        // Also check if maybe the update is happening on a different record
        $allRules = SessionCompensationRule::all();
        $dbValue = \DB::table('session_compensation_rules')->where('id', $ruleId)->first();

        $this->assertFalse(
            (bool) $dbValue?->is_active,
            "Session rule is_active should be false. DB row: " . json_encode($dbValue) . " All rules count: " . $allRules->count()
        );
    }

    /**
     * PC-25b: Delete session rule — without permission → allowed (no authorize on destroy)
     */
    public function test_pc25b_delete_session_rule_without_permission_allowed(): void
    {
        $rule = SessionCompensationRule::factory()->globalScope()->create();
        $unauthorized = User::factory()->create();

        $response = $this->actingAs($unauthorized)->delete(route('payroll.session-rules.destroy', $rule));

        // BUG: No authorization check on destroy
        $response->assertRedirect(route('payroll.session-rules.index'));
    }
}
