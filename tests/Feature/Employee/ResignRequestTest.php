<?php

namespace Tests\Feature\Employee;

use App\Models\Employee;
use App\Models\EmploymentStatus;
use App\Models\Position;
use App\Models\ResignRequest;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

// Flow: employee-offboarding / resign-request (EO-01 → EO-06)
// Covers: submit resign, index auth, approve/reject, resign_date validation, ownership
class ResignRequestTest extends TestCase
{
    private User $employeeUser;

    private Employee $employee;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->employee = Employee::factory()->create();
        $this->employeeUser = User::factory()->create([
            'employee_id' => $this->employee->id,
            'email' => $this->employee->email,
        ]);

        $position = Position::firstOrCreate(
            ['position_name' => 'Staff'],
            ['role_id' => Role::where('name', 'HRR')->first()?->id ?? 1, 'hierarchy_order' => 1]
        );

        EmploymentStatus::create([
            'employees_id' => $this->employee->id,
            'type_employment' => 'Kontrak',
            'join_date' => now()->subYear(),
            'position_id' => $position->id,
            'contract_start_date' => now()->subYear(),
            'contract_end_date' => now()->addMonths(3),
        ]);
    }

    // EO-01: Employee submit resign request → creates with status 'pending'
    public function test_employee_submit_resign_request(): void
    {
        $resignDate = now()->addMonths(2)->format('Y-m-d');

        $response = $this->actingAs($this->employeeUser)
            ->post(route('resign-requests.store', $this->employee), [
                'resign_date' => $resignDate,
                'reason' => 'Peluang karir baru',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('resign_requests', [
            'employee_id' => $this->employee->id,
            'status' => 'pending',
            'resign_date' => $resignDate,
        ]);
    }

    // EO-02: Index requires contract.manage permission
    public function test_index_requires_contract_manage(): void
    {
        $contractPerm = Permission::firstOrCreate(['name' => 'contract.manage', 'guard_name' => 'web']);

        // User without permission → 403
        $unauthorized = User::factory()->create();
        $this->actingAs($unauthorized)
            ->get(route('resign-requests.index'))
            ->assertStatus(403);

        // HR with permission → 200
        $hr = User::factory()->create();
        $hr->givePermissionTo($contractPerm);
        $this->actingAs($hr)
            ->get(route('resign-requests.index'))
            ->assertOk();
    }

    // EO-03: Approve resign requires contract.manage (via ResignRequestPolicy)
    public function test_approve_requires_contract_manage(): void
    {
        $resign = ResignRequest::create([
            'employee_id' => $this->employee->id,
            'resign_date' => now()->addMonths(2),
            'reason' => 'Moving on',
            'status' => 'pending',
        ]);

        $contractPerm = Permission::firstOrCreate(['name' => 'contract.manage', 'guard_name' => 'web']);
        $hr = User::factory()->create();
        $hr->givePermissionTo($contractPerm);

        $this->actingAs($hr)
            ->post(route('resign-requests.approve', $resign))
            ->assertRedirect();

        $this->assertDatabaseHas('resign_requests', [
            'id' => $resign->id,
            'status' => 'approved',
            'reviewed_by' => $hr->id,
        ]);
    }

    // EO-03b: User without contract.manage cannot approve
    public function test_user_without_permission_cannot_approve(): void
    {
        $resign = ResignRequest::create([
            'employee_id' => $this->employee->id,
            'resign_date' => now()->addMonths(2),
            'status' => 'pending',
        ]);

        $unauthorized = User::factory()->create();
        $this->actingAs($unauthorized)
            ->post(route('resign-requests.approve', $resign))
            ->assertStatus(403);
    }

    // EO-04: Reject resign
    public function test_reject_resign(): void
    {
        $resign = ResignRequest::create([
            'employee_id' => $this->employee->id,
            'resign_date' => now()->addMonths(2),
            'reason' => 'Test',
            'status' => 'pending',
        ]);

        $contractPerm = Permission::firstOrCreate(['name' => 'contract.manage', 'guard_name' => 'web']);
        $hr = User::factory()->create();
        $hr->givePermissionTo($contractPerm);

        $this->actingAs($hr)
            ->post(route('resign-requests.reject', $resign))
            ->assertRedirect();

        $this->assertDatabaseHas('resign_requests', [
            'id' => $resign->id,
            'status' => 'rejected',
            'reviewed_by' => $hr->id,
        ]);
    }

    // EO-05: resign_date must be after today
    public function test_resign_date_must_be_after_today(): void
    {
        $this->actingAs($this->employeeUser)
            ->post(route('resign-requests.store', $this->employee), [
                'resign_date' => now()->format('Y-m-d'), // today, not after
                'reason' => 'Test',
            ])
            ->assertSessionHasErrors('resign_date');
    }

    // EO-05b: Past date also rejected
    public function test_resign_date_past_rejected(): void
    {
        $this->actingAs($this->employeeUser)
            ->post(route('resign-requests.store', $this->employee), [
                'resign_date' => now()->subDay()->format('Y-m-d'),
                'reason' => 'Test',
            ])
            ->assertSessionHasErrors('resign_date');
    }

    // EO-06: Cannot submit resign for another employee
    // ResignRequestFormRequest::authorize checks auth()->user()->id === $employee->user_id
    public function test_cannot_submit_resign_for_another_employee(): void
    {
        $otherEmployee = Employee::factory()->create();
        $otherUser = User::factory()->create([
            'employee_id' => $otherEmployee->id,
            'email' => $otherEmployee->email,
        ]);

        $this->actingAs($otherUser)
            ->post(route('resign-requests.store', $this->employee), [
                'resign_date' => now()->addMonth()->format('Y-m-d'),
                'reason' => 'Hacked',
            ])
            ->assertStatus(403);
    }

    // EO-06b: Employee CAN submit resign for themselves (via create route)
    public function test_employee_can_access_own_resign_create(): void
    {
        $this->actingAs($this->employeeUser)
            ->get(route('resign-requests.create', $this->employee))
            ->assertOk();
    }

    // Auth: Unauthenticated → redirect login
    public function test_unauthenticated_redirect_to_login(): void
    {
        $this->get(route('resign-requests.create', $this->employee))
            ->assertRedirect(route('login'));
    }
}
