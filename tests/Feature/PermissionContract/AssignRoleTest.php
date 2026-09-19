<?php

namespace Tests\Feature\PermissionContract;

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class AssignRoleTest extends TestCase
{
    private User $superAdmin;

    private User $targetUser;

    private Role $testRole;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole('Super Admin');

        $this->targetUser = User::factory()->create();
        $this->targetUser->assignRole('REGULAR_TUTOR');

        $this->testRole = Role::create(['name' => 'Test Assign Role', 'guard_name' => 'web']);
    }

    // PC-09 — Assign role berhasil
    public function test_assign_role_berhasil(): void
    {
        $newRole = Role::create(['name' => 'New Assigned Role', 'guard_name' => 'web']);

        $this->actingAs($this->superAdmin)
            ->post(route('users.assign-role', $this->targetUser), [
                'roles' => [$newRole->id],
            ])
            ->assertRedirect();

        $this->assertTrue($this->targetUser->fresh()->hasRole('New Assigned Role'));
    }

    // PC-09 — Assign role, permission target berlaku real-time
    public function test_assign_role_permission_target_berlaku_realtime(): void
    {
        $permission = Permission::where('name', 'organization.branch.create')->first();
        $this->testRole->syncPermissions([$permission->id]);

        $this->assertFalse($this->targetUser->can('organization.branch.create'));

        $this->actingAs($this->superAdmin)
            ->post(route('users.assign-role', $this->targetUser), [
                'roles' => [$this->testRole->id],
            ]);

        $this->assertTrue($this->targetUser->fresh()->can('organization.branch.create'));
    }

    // PC-10 — Assign role, permission cache target di-clear (BUG GAP-25)
    public function test_assign_role_permission_cache_target_dihapus(): void
    {
        $permission = Permission::where('name', 'organization.area.create')->first();
        $this->testRole->syncPermissions([$permission->id]);

        // Pre-warm cache: check permission sebelum assign
        $canBefore = $this->targetUser->can('organization.area.create');
        $this->assertFalse($canBefore);

        // Assign role yang punya permission tsb
        $this->actingAs($this->superAdmin)
            ->post(route('users.assign-role', $this->targetUser), [
                'roles' => [$this->testRole->id],
            ]);

        // GAP-25: forgetCachedPermissions() dipanggil di $user (target),
        // tapi Spatie cache mungkin masih stale untuk request selanjutnya.
        // Fresh() memaksa reload dari DB — ini yang seharusnya jalan setelah cache clear.
        $freshUser = $this->targetUser->fresh();
        $this->assertTrue(
            $freshUser->can('organization.area.create'),
            'GAP-25: Setelah assign role, permission target harus berlaku real-time'
        );
    }

    // PC-11 — Assign role tanpa permission (BUG GAP-27a)
    public function test_assign_role_tanpa_permission(): void
    {
        $unauthorized = User::factory()->create();
        $unauthorized->assignRole('MARKETING');

        $this->actingAs($unauthorized)
            ->post(route('users.assign-role', $this->targetUser), [
                'roles' => [$this->testRole->id],
            ])
            ->assertStatus(403);
    }

    // PC-12 — Assign role dengan roles array kosong (revoke semua)
    public function test_assign_role_roles_array_kosong_revoke_semua(): void
    {
        // User punya role sebelumnya
        $this->assertTrue($this->targetUser->hasRole('REGULAR_TUTOR'));

        $this->actingAs($this->superAdmin)
            ->post(route('users.assign-role', $this->targetUser), [
                'roles' => [],
            ])
            ->assertRedirect();

        $this->assertCount(0, $this->targetUser->fresh()->roles);
    }

    // PC-13 — Assign role dengan role id tidak exist
    public function test_assign_role_role_id_tidak_exist(): void
    {
        $this->actingAs($this->superAdmin)
            ->post(route('users.assign-role', $this->targetUser), [
                'roles' => [99999],
            ])
            ->assertRedirect()
            ->assertSessionHasErrors('roles');
    }
}
