<?php

namespace Tests\Feature\Attendance;

use App\Models\Employee;
use App\Models\EmploymentStatus;
use App\Models\Position;
use App\Models\User;
use App\Models\WorkScheduleAssignment;
use App\Models\WorkScheduleRule;
use App\Services\Attendance\WorkScheduleService;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class WorkScheduleAssignmentTest extends TestCase
{
    private User $authorizedUser;

    private User $unauthorizedUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authorizedUser = User::factory()->create();
        $this->authorizedUser->givePermissionTo('attendance.schedule_rule.manage');

        $this->unauthorizedUser = User::factory()->create();
        $this->unauthorizedUser->assignRole('Tutor');
    }

    private function createEmployeeWithUser(?Role $role = null): array
    {
        $employee = Employee::factory()->create();

        $user = User::factory()->create([
            'employee_id' => $employee->id,
        ]);

        if ($role) {
            $user->assignRole($role->name);
        }

        // Refresh employee so user relationship is not cached
        $employee = $employee->fresh();

        return [$employee, $user];
    }

    // WS-16 — Assign schedule via controller (stores short type names)
    public function test_assign_schedule_ke_employee(): void
    {
        $rule = WorkScheduleRule::factory()->create();
        $role = Role::first();

        $this->actingAs($this->authorizedUser)
            ->post(route('work-schedule-assignments.store'), [
                'work_schedule_rule_id' => $rule->id,
                'assignable_type' => 'role',
                'assignable_id' => $role->id,
                'effective_start_date' => now()->toDateString(),
                'is_active' => true,
            ])
            ->assertRedirect();

        // Controller converts 'role' → Role::class (FQCN) via TYPE_MAP before storing
        $this->assertTrue(
            WorkScheduleAssignment::where('work_schedule_rule_id', $rule->id)
                ->where('assignable_type', Role::class)
                ->where('assignable_id', $role->id)
                ->exists(),
            'Assignment harus tersimpan di database'
        );
    }

    // WS-16b — service pakai short type ('role') sesuai controller — resolve berhasil
    public function test_controller_stores_short_type_and_service_resolves_correctly(): void
    {
        $rule = WorkScheduleRule::factory()->create();
        $role = Role::first();

        // Store via controller — short type 'role'
        $this->actingAs($this->authorizedUser)
            ->post(route('work-schedule-assignments.store'), [
                'work_schedule_rule_id' => $rule->id,
                'assignable_type' => 'role',
                'assignable_id' => $role->id,
                'effective_start_date' => now()->toDateString(),
                'is_active' => true,
            ])
            ->assertRedirect();

        [$employee] = $this->createEmployeeWithUser($role);

        $service = app(WorkScheduleService::class);
        $resolved = $service->getCurrentScheduleForEmployee($employee);

        $this->assertNotNull($resolved, 'Service harus bisa resolve role assignment (FQCN match).');
        $this->assertEquals($rule->id, $resolved->id);
    }

    // WS-17 — Resolve schedule: priority employee > position > role
    public function test_resolve_schedule_priority_employee_highest(): void
    {
        $ruleForEmployee = WorkScheduleRule::factory()->create(['setting_name' => 'Employee Rule']);
        $ruleForRole = WorkScheduleRule::factory()->create(['setting_name' => 'Role Rule']);

        $role = Role::first();

        // Assign ke role
        WorkScheduleAssignment::create([
            'work_schedule_rule_id' => $ruleForRole->id,
            'assignable_type' => Role::class,
            'assignable_id' => $role->id,
            'effective_start_date' => now()->toDateString(),
            'is_active' => true,
        ]);

        [$employee] = $this->createEmployeeWithUser($role);

        // Assign ke employee langsung
        WorkScheduleAssignment::create([
            'work_schedule_rule_id' => $ruleForEmployee->id,
            'assignable_type' => Employee::class,
            'assignable_id' => $employee->id,
            'effective_start_date' => now()->toDateString(),
            'is_active' => true,
        ]);

        // Resolve via service — employee-level harus menang
        $service = app(WorkScheduleService::class);
        $resolved = $service->getCurrentScheduleForEmployee($employee->fresh());

        $this->assertNotNull($resolved);
        $this->assertEquals('Employee Rule', $resolved->setting_name);
    }

    // WS-18 — Resolve schedule: fallback ke position
    public function test_resolve_schedule_fallback_ke_position(): void
    {
        $ruleForPosition = WorkScheduleRule::factory()->create(['setting_name' => 'Position Rule']);
        $ruleForRole = WorkScheduleRule::factory()->create(['setting_name' => 'Role Rule']);

        $role = Role::first();

        WorkScheduleAssignment::create([
            'work_schedule_rule_id' => $ruleForRole->id,
            'assignable_type' => Role::class,
            'assignable_id' => $role->id,
            'effective_start_date' => now()->toDateString(),
            'is_active' => true,
        ]);

        $position = Position::create([
            'position_name' => 'Test Position',
            'role_id' => $role->id,
            'hierarchy_order' => 99,
        ]);

        WorkScheduleAssignment::create([
            'work_schedule_rule_id' => $ruleForPosition->id,
            'assignable_type' => Position::class,
            'assignable_id' => $position->id,
            'effective_start_date' => now()->toDateString(),
            'is_active' => true,
        ]);

        // Buat employee dengan position (tanpa direct assignment)
        [$employee] = $this->createEmployeeWithUser($role);

        EmploymentStatus::create([
            'employees_id' => $employee->id,
            'type_employment' => 'permanent',
            'position_id' => $position->id,
        ]);

        // Resolve — position harus menang atas role
        $service = app(WorkScheduleService::class);
        $resolved = $service->getCurrentScheduleForEmployee($employee->fresh());

        $this->assertNotNull($resolved);
        $this->assertEquals('Position Rule', $resolved->setting_name);
    }

    // WS-19 — Resolve schedule: fallback ke role
    public function test_resolve_schedule_fallback_ke_role(): void
    {
        $ruleForRole = WorkScheduleRule::factory()->create(['setting_name' => 'Role Only Rule']);

        $role = Role::first();

        // Buat position yang terkait role ini (employee -> position -> role)
        $position = Position::create([
            'position_name' => 'WS Test Position',
            'role_id' => $role->id,
            'hierarchy_order' => 99,
        ]);

        WorkScheduleAssignment::create([
            'work_schedule_rule_id' => $ruleForRole->id,
            'assignable_type' => Role::class,
            'assignable_id' => $role->id,
            'effective_start_date' => now()->toDateString(),
            'is_active' => true,
        ]);

        // Employee: punya User dengan Spatie role + punya Position via EmploymentStatus
        [$employee, $user] = $this->createEmployeeWithUser($role);

        EmploymentStatus::create([
            'employees_id' => $employee->id,
            'type_employment' => 'permanent',
            'position_id' => $position->id,
        ]);

        // Resolve: no employee-level, no position-level → fallback ke role
        $fresh = Employee::with('user.roles', 'currentStatus.position')->find($employee->id);

        $service = app(WorkScheduleService::class);
        $resolved = $service->getCurrentScheduleForEmployee($fresh);

        $this->assertNotNull($resolved, 'Service harus resolve ke role assignment');
        $this->assertEquals('Role Only Rule', $resolved->setting_name);
    }

    // WS-19b — FIXED: Service sekarang pakai Spatie\Permission\Models\Role (FQCN konsisten)
    public function test_service_resolves_spatie_role_assignment(): void
    {
        $rule = WorkScheduleRule::factory()->create(['setting_name' => 'Spatie Role Assignment']);
        $role = Role::first();

        WorkScheduleAssignment::create([
            'work_schedule_rule_id' => $rule->id,
            'assignable_type' => Role::class,
            'assignable_id' => $role->id,
            'effective_start_date' => now()->toDateString(),
            'is_active' => true,
        ]);

        [$employee] = $this->createEmployeeWithUser($role);

        $service = app(WorkScheduleService::class);
        $resolved = $service->getCurrentScheduleForEmployee($employee->fresh());

        $this->assertNotNull($resolved, 'Service harus resolve Spatie role assignment (FQCN konsisten)');
        $this->assertEquals($rule->id, $resolved->id);
    }

    // WS-20 — Resolve schedule: tidak ada assignment apapun
    public function test_resolve_schedule_tidak_ada_assignment(): void
    {
        $employee = Employee::factory()->create();

        $service = app(WorkScheduleService::class);
        $resolved = $service->getCurrentScheduleForEmployee($employee);

        $this->assertNull($resolved);
    }
}
