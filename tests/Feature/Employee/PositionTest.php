<?php

namespace Tests\Feature\Employee;

use App\Models\Employee;
use App\Models\EmploymentStatus;
use App\Models\Position;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

// Flow: position-management (POS-01 to POS-06)
class PositionTest extends TestCase
{
    private User $userWithPermission;

    private User $userWithoutPermission;

    private Role $role;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->role = Role::where('name', 'HRR')->first();

        $this->userWithPermission = User::factory()->create();
        $this->userWithPermission->givePermissionTo('position.manage');

        $this->userWithoutPermission = User::factory()->create();
    }

    // POS-01: Buat posisi berhasil
    public function test_user_dengan_permission_bisa_buat_posisi(): void
    {
        $response = $this->actingAs($this->userWithPermission)
            ->post(route('positions.store'), [
                'position_name' => 'Manager Area',
                'role_id' => $this->role->id,
                'hierarchy_order' => 5,
                'permission_ids' => [],
            ]);

        $response->assertRedirect(route('positions.index'));
        $this->assertDatabaseHas('positions', ['position_name' => 'Manager Area']);
    }

    // POS-02: user tanpa permission → 403 (middleware can:position.manage)
    public function test_user_tanpa_permission_tidak_bisa_akses_posisi(): void
    {
        $this->actingAs($this->userWithoutPermission)
            ->get(route('positions.index'))
            ->assertStatus(403);

        $this->actingAs($this->userWithoutPermission)
            ->post(route('positions.store'), [
                'position_name' => 'Test',
                'role_id' => $this->role->id,
                'hierarchy_order' => 1,
            ])
            ->assertStatus(403);
    }

    // POS-03: Assign permissions ke posisi tersync
    public function test_assign_permissions_ke_posisi_tersync(): void
    {
        $permission = Permission::where('name', 'employee.view')->first();
        $position = Position::create([
            'position_name' => 'Staff HR',
            'role_id' => $this->role->id,
            'hierarchy_order' => 8,
        ]);

        $this->actingAs($this->userWithPermission)
            ->patch(route('positions.update', $position), [
                'position_name' => 'Staff HR',
                'role_id' => $this->role->id,
                'hierarchy_order' => 8,
                'permission_ids' => [$permission->id],
            ])
            ->assertRedirect(route('positions.index'));

        $this->assertTrue($position->fresh()->permissions->contains($permission));
    }

    // POS-04: forgetCachedPermissions dipanggil saat update posisi (tidak error 500)
    public function test_update_posisi_clear_permission_cache_role_terkait(): void
    {
        $permission = Permission::where('name', 'employee.view')->first();
        $position = Position::create([
            'position_name' => 'Staff',
            'role_id' => $this->role->id,
            'hierarchy_order' => 9,
        ]);

        $this->actingAs($this->userWithPermission)
            ->patch(route('positions.update', $position), [
                'position_name' => 'Staff Updated',
                'role_id' => $this->role->id,
                'hierarchy_order' => 9,
                'permission_ids' => [$permission->id],
            ])
            ->assertRedirect(route('positions.index'));

        $this->assertDatabaseHas('positions', ['position_name' => 'Staff Updated']);
    }

    // POS-05: Hapus posisi masih ada employment_status aktif → diblokir
    public function test_hapus_posisi_dengan_employee_aktif_diblokir(): void
    {
        $position = Position::create([
            'position_name' => 'Posisi Aktif',
            'role_id' => $this->role->id,
            'hierarchy_order' => 10,
        ]);

        $employee = Employee::factory()->create();
        EmploymentStatus::create([
            'employees_id' => $employee->id,
            'type_employment' => 'Tetap',
            'join_date' => now()->subYear(),
            'position_id' => $position->id,
            'setup_incomplete' => false,
        ]);

        $this->actingAs($this->userWithPermission)
            ->delete(route('positions.destroy', $position))
            ->assertRedirect(route('positions.index'));

        $this->assertDatabaseHas('positions', ['id' => $position->id]);
    }

    // Hapus posisi tanpa employee → berhasil
    public function test_hapus_posisi_tanpa_employee_berhasil(): void
    {
        $position = Position::create([
            'position_name' => 'Posisi Kosong',
            'role_id' => $this->role->id,
            'hierarchy_order' => 11,
        ]);

        $this->actingAs($this->userWithPermission)
            ->delete(route('positions.destroy', $position))
            ->assertRedirect(route('positions.index'));

        $this->assertDatabaseMissing('positions', ['id' => $position->id]);
    }

    // Tanpa login → redirect ke login
    public function test_tanpa_login_redirect_ke_login(): void
    {
        $this->get(route('positions.index'))
            ->assertRedirect(route('login'));
    }
}
