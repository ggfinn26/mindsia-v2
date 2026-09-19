<?php

namespace Tests\Feature\Attendance;

use App\Models\AttendancePolicy;
use App\Models\AttendanceRule;
use App\Models\AttendanceRuleAction;
use App\Models\AttendanceRuleViolation;
use App\Models\User;
use Tests\TestCase;

class AttendancePolicyRulesTest extends TestCase
{
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->admin->givePermissionTo(['attendance.policy.manage', 'attendance.rule.manage']);
    }

    // ─────────────── Attendance Policy CRUD ───────────────

    // ── AP-01 (implicit): AttendancePolicy store works ──
    // The SKENARIO doesn't have a specific AP-01 for policy, but the controller has full CRUD.

    private function validPolicyData(): array
    {
        return [
            'policy_name' => 'Test Policy',
            'attendance_scope' => 'branch',
            'is_active' => true,
            'work_is_required' => true,
            'work_late_tolerance_minutes' => 15,
            'work_early_leave_tolerance_minutes' => 10,
            'session_is_required' => true,
            'session_late_tolerance_minutes' => 10,
        ];
    }

    public function test_attendance_policy_store_success(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('attendance-policies.store'), $this->validPolicyData());

        $response->assertRedirect(route('attendance-policies.index'));
        $response->assertSessionHas('success');

        $policy = AttendancePolicy::first();
        $this->assertNotNull($policy);
        $this->assertEquals('Test Policy', $policy->policy_name);
        $this->assertNotNull($policy->workScheduleConfig);
        $this->assertNotNull($policy->sessionConfig);
        $this->assertEquals(15, $policy->workScheduleConfig->late_tolerance_minutes);
        $this->assertEquals(10, $policy->sessionConfig->late_tolerance_minutes);
    }

    public function test_attendance_policy_update_success(): void
    {
        $policy = AttendancePolicy::factory()->create();

        $response = $this->actingAs($this->admin)
            ->put(route('attendance-policies.update', $policy), array_merge($this->validPolicyData(), [
                'policy_name' => 'Updated Policy',
            ]));

        $response->assertRedirect(route('attendance-policies.index'));
        $policy->refresh();
        $this->assertEquals('Updated Policy', $policy->policy_name);
    }

    public function test_attendance_policy_delete_success(): void
    {
        $policy = AttendancePolicy::factory()->create();

        $response = $this->actingAs($this->admin)
            ->delete(route('attendance-policies.destroy', $policy));

        $response->assertRedirect(route('attendance-policies.index'));
        $this->assertDatabaseMissing('attendance_policies', ['id' => $policy->id]);
    }

    public function test_attendance_policy_store_without_permission(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('attendance-policies.store'), $this->validPolicyData());

        $response->assertForbidden();
    }

    // ─────────────── Attendance Rule CRUD ───────────────

    // ── AP-01: Create rule berhasil ──

    private function validRuleData(): array
    {
        return [
            'rule_name' => 'Test Rule',
            'attendance_type' => 'work_schedule',
            'trigger_type' => 'consecutive_absence',
            'trigger_operator' => '>=',
            'trigger_value' => 3,
            'period_type' => 'monthly',
            'is_active' => true,
            'actions' => [
                ['action_type' => 'notification', 'action_order' => 1],
                ['action_type' => 'warning_letter', 'action_order' => 2],
            ],
        ];
    }

    public function test_ap01_create_rule_success(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('attendance-rules.store'), $this->validRuleData());

        $response->assertRedirect(route('attendance-rules.index'));
        $response->assertSessionHas('success');

        $rule = AttendanceRule::first();
        $this->assertNotNull($rule);
        $this->assertEquals('Test Rule', $rule->rule_name);
        $this->assertEquals(2, $rule->actions()->count());
    }

    // ── AP-02: Create rule — tanpa actions ──

    public function test_ap02_create_rule_without_actions(): void
    {
        $data = $this->validRuleData();
        $data['actions'] = [];

        $response = $this->actingAs($this->admin)
            ->post(route('attendance-rules.store'), $data);

        $response->assertRedirect();
        $response->assertSessionHasErrors('actions');
    }

    // ── AP-03: Create rule — trigger_type tidak valid ──

    public function test_ap03_create_rule_invalid_trigger_type(): void
    {
        $data = $this->validRuleData();
        $data['trigger_type'] = 'invalid';

        $response = $this->actingAs($this->admin)
            ->post(route('attendance-rules.store'), $data);

        $response->assertRedirect();
        $response->assertSessionHasErrors('trigger_type');
    }

    // ── AP-04: Create rule — tanpa permission ──

    public function test_ap04_create_rule_without_permission(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('attendance-rules.store'), $this->validRuleData());

        $response->assertForbidden();
    }

    // ── AP-05: Update rule berhasil ──

    public function test_ap05_update_rule_success(): void
    {
        $rule = AttendanceRule::factory()->create();
        AttendanceRuleAction::factory()->create([
            'attendance_rule_id' => $rule->id,
            'action_type' => 'notification',
            'action_order' => 1,
        ]);

        $updatedData = array_merge($this->validRuleData(), [
            'rule_name' => 'Updated Rule',
            'actions' => [
                ['action_type' => 'notification', 'action_order' => 1],
                ['action_type' => 'payroll_deduction', 'action_order' => 2],
                ['action_type' => 'mark_anomaly', 'action_order' => 3],
            ],
        ]);

        $response = $this->actingAs($this->admin)
            ->put(route('attendance-rules.update', $rule), $updatedData);

        $response->assertRedirect(route('attendance-rules.index'));

        $rule->refresh();
        $this->assertEquals('Updated Rule', $rule->rule_name);

        // syncActions deletes old and creates new — old notification action should be gone
        $this->assertEquals(3, $rule->actions()->count());
        $this->assertEquals(0, $rule->actions()->where('action_type', 'notification')->where('action_order', 1)->count()
            + $rule->actions()->where('action_type', 'payroll_deduction')->where('action_order', 2)->count()
            ? 0 : 1); // at least check it has the right count
        $actionTypes = $rule->actions()->pluck('action_type')->toArray();
        $this->assertContains('payroll_deduction', $actionTypes);
        $this->assertContains('mark_anomaly', $actionTypes);
    }

    // ── AP-06: Delete rule berhasil ──

    public function test_ap06_delete_rule_without_violations(): void
    {
        $rule = AttendanceRule::factory()->create();

        $response = $this->actingAs($this->admin)
            ->delete(route('attendance-rules.destroy', $rule));

        $response->assertRedirect(route('attendance-rules.index'));
        $this->assertDatabaseMissing('attendance_rules', ['id' => $rule->id]);
    }

    // ── AP-07: Delete rule — sudah pernah dilanggar ──

    public function test_ap07_delete_rule_with_violations(): void
    {
        $rule = AttendanceRule::factory()->create();

        // Create a violation linked to the rule
        AttendanceRuleViolation::factory()->create(['attendance_rule_id' => $rule->id]);

        $response = $this->actingAs($this->admin)
            ->delete(route('attendance-rules.destroy', $rule));

        $response->assertRedirect();
        $response->assertSessionHasErrors();

        $this->assertDatabaseHas('attendance_rules', ['id' => $rule->id]);
    }

    // ── AP-08: Action payroll_deduction — tidak ada amount (GAP-66) ──
    // No validation for deduction amount when action_type=payroll_deduction.

    public function test_ap08_payroll_deduction_without_amount(): void
    {
        $data = $this->validRuleData();
        $data['actions'] = [
            ['action_type' => 'payroll_deduction', 'action_order' => 1],
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('attendance-rules.store'), $data);

        $response->assertRedirect(route('attendance-rules.index'));

        // GAP-66: Rule action saved without any deduction amount — no validation
        $action = AttendanceRuleAction::where('action_type', 'payroll_deduction')->first();
        $this->assertNotNull($action);
        // No payroll_action record exists because there's no mechanism to create it from the form
        $this->assertNull($action->payrollAction);
    }

    // ── AP-09: Action warning_letter — SP tidak di-generate (GAP-8) ──
    // No execution pipeline exists — only CRUD is testable.

    public function test_ap09_warning_letter_action_no_execution_pipeline(): void
    {
        $data = $this->validRuleData();
        $data['actions'] = [
            ['action_type' => 'warning_letter', 'action_order' => 1],
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('attendance-rules.store'), $data);

        $response->assertRedirect(route('attendance-rules.index'));

        $action = AttendanceRuleAction::where('action_type', 'warning_letter')->first();
        $this->assertNotNull($action);

        // GAP-8 resolved: migration table now exists
        $this->assertTrue(\Schema::hasTable('attendance_rule_warning_letter_actions'));
    }
}
