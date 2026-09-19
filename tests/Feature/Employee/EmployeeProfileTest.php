<?php

namespace Tests\Feature\Employee;

use App\Models\Branch;
use App\Models\Employee;
use App\Models\User;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

// Flow: employee-profile (EP-01 to EP-06)
class EmployeeProfileTest extends TestCase
{
    private User $globalViewer;

    private User $noPermissionUser;

    private Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // User dengan employee.view global (tidak punya linked employee → scope global)
        $this->globalViewer = User::factory()->create();
        $this->globalViewer->givePermissionTo(['employee.view', 'employee.create', 'employee.update']);

        $this->noPermissionUser = User::factory()->create();

        $this->branch = Branch::factory()->create();
    }

    private function branchRegionId(): int
    {
        return $this->branch->area->region_id;
    }

    // EP-01: user dengan employee.view bisa list semua employee (global scope)
    public function test_user_dengan_view_permission_bisa_list_employee(): void
    {
        Employee::factory()->create(); // 1 cukup untuk test 200 OK

        $this->actingAs($this->globalViewer)
            ->get(route('employees.index'))
            ->assertOk();
    }

    // EP-02: user scoped ke branch hanya lihat employee di branchnya
    public function test_user_scoped_branch_hanya_lihat_employee_di_branch_sendiri(): void
    {
        $scopedEmployee = Employee::factory()->create(['branch_id' => $this->branch->id]);
        $otherBranch = Branch::factory()->create();
        Employee::factory()->create(['branch_id' => $otherBranch->id]);

        // Buat user yang punya employee terikat ke branch tertentu
        $scopedUser = User::factory()->create();
        $scopedUser->givePermissionTo('employee.view');
        $linkedEmployee = Employee::factory()->create(['branch_id' => $this->branch->id]);
        $linkedEmployee->user()->save($scopedUser);
        $scopedUser->refresh();

        $response = $this->actingAs($scopedUser)
            ->get(route('employees.index'));

        $response->assertOk();
        // Hanya employee di branch ini yang tampil (EP-02: scope filter bekerja)
        $response->assertViewHas('employees', function ($paginator) use ($scopedEmployee, $otherBranch) {
            $ids = $paginator->pluck('id')->toArray();

            return in_array($scopedEmployee->id, $ids)
                && ! $paginator->contains(fn ($e) => $e->branch_id === $otherBranch->id);
        });
    }

    // EP-03: user dengan employee.create bisa buat employee baru
    public function test_user_dengan_create_permission_bisa_buat_employee(): void
    {
        $response = $this->actingAs($this->globalViewer)
            ->post(route('employees.store'), [
                'employee_code' => 'EMP001',
                'full_name' => 'Budi Santoso',
                'gender' => 'L',
                'birthdate' => '1990-01-01',
                'email' => 'budi@mindsia.test',
                'whatsapp_number' => '6281234567890',
                'branch_id' => $this->branch->id,
                'region_id' => $this->branchRegionId(),
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('employees', ['employee_code' => 'EMP001']);
    }

    // EP-04: update email dengan email sendiri tidak error unique (self-update)
    public function test_update_email_sendiri_tidak_trigger_unique_error(): void
    {
        $employee = Employee::factory()->create([
            'email' => 'siti@mindsia.test',
            'branch_id' => $this->branch->id,
        ]);

        // Update dengan email yang sama → harus sukses (tidak error unique)
        $response = $this->actingAs($this->globalViewer)
            ->patch(route('employees.update', $employee), [
                'employee_code' => $employee->employee_code,
                'full_name' => $employee->full_name,
                'gender' => $employee->gender,
                'birthdate' => $employee->birthdate->format('Y-m-d'),
                'email' => 'siti@mindsia.test',
                'whatsapp_number' => $employee->whatsapp_number,
                'branch_id' => $this->branch->id,
            ]);

        $response->assertSessionMissing('errors');
        $this->assertDatabaseHas('employees', ['email' => 'siti@mindsia.test']);
    }

    // EP-05: user tanpa permission → 403 (EmployeePolicy::viewAny checks employee.view)
    public function test_user_tanpa_permission_ditolak_403(): void
    {
        $this->actingAs($this->noPermissionUser)
            ->get(route('employees.index'))
            ->assertStatus(403);
    }

    // Validasi: field wajib kosong → 422
    public function test_buat_employee_tanpa_field_wajib_ditolak(): void
    {
        $this->actingAs($this->globalViewer)
            ->post(route('employees.store'), [])
            ->assertSessionHasErrors(['employee_code', 'full_name', 'gender', 'email', 'branch_id']);
    }

    // Validasi: employee_code duplikat → error unique
    public function test_employee_code_duplikat_ditolak(): void
    {
        Employee::factory()->create(['employee_code' => 'EMP-DUPLIKAT']);

        $this->actingAs($this->globalViewer)
            ->post(route('employees.store'), [
                'employee_code' => 'EMP-DUPLIKAT',
                'full_name' => 'Test',
                'gender' => 'L',
                'birthdate' => '1990-01-01',
                'email' => 'unik@mindsia.test',
                'whatsapp_number' => '6281234567890',
                'branch_id' => $this->branch->id,
            ])
            ->assertSessionHasErrors('employee_code');
    }

    // Tanpa login → redirect ke login
    public function test_tanpa_login_redirect_ke_login(): void
    {
        $this->get(route('employees.index'))
            ->assertRedirect(route('login'));
    }
}
