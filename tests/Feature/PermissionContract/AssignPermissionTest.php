<?php

namespace Tests\Feature\PermissionContract;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AssignPermissionTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;

    private User $boardUser;

    private User $targetUser;

    protected function setUp(): void
    {
        parent::setUp();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();


        

        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole('Super Admin');

        // BOARD_OF_DIRECTORS = CEO role (punya semua permission via role, bukan direct)
        $this->boardUser = User::factory()->create();
        $this->boardUser->assignRole('CEO');

        $this->targetUser = User::factory()->create();
        $this->targetUser->assignRole('REGULAR_TUTOR');
    }

    // PC-14 — Assign permission berhasil
    public function test_assign_permission_berhasil(): void
    {
        $permission = Permission::where('name', 'organization.branch.create')->first();

        $this->actingAs($this->superAdmin)
            ->post(route('users.assign-permission', $this->targetUser), [
                'permissions' => [$permission->id],
            ])
            ->assertRedirect();

        $this->assertTrue($this->targetUser->fresh()->hasDirectPermission('organization.branch.create'));
    }

    // PC-14 — Assign permission, berlaku real-time
    public function test_assign_permission_berlaku_realtime(): void
    {
        $permission = Permission::where('name', 'organization.area.create')->first();

        $this->assertFalse($this->targetUser->can('organization.area.create'));

        $this->actingAs($this->superAdmin)
            ->post(route('users.assign-permission', $this->targetUser), [
                'permissions' => [$permission->id],
            ]);

        $this->assertTrue($this->targetUser->fresh()->can('organization.area.create'));
    }

    // PC-15 — Assign permission, cache target di-clear (BUG GAP-25)
    public function test_assign_permission_cache_target_dihapus(): void
    {
        $permission = Permission::where('name', 'organization.institution.create')->first();

        // Pre-warm cache
        $canBefore = $this->targetUser->can('organization.institution.create');
        $this->assertFalse($canBefore);

        $this->actingAs($this->superAdmin)
            ->post(route('users.assign-permission', $this->targetUser), [
                'permissions' => [$permission->id],
            ]);

        // GAP-25: forgetCachedPermissions() dipanggil — verifikasi cache ter-clear
        $freshUser = $this->targetUser->fresh();
        $this->assertTrue(
            $freshUser->can('organization.institution.create'),
            'GAP-25: Setelah assign permission, permission target harus berlaku real-time'
        );
    }

    // PC-16 — Assign permission oleh BOARD (bukan superadmin/IT) — BUG GAP-27b
    public function test_assign_permission_oleh_board_bukan_superadmin(): void
    {
        $permission = Permission::where('name', 'organization.province.create')->first();

        // BOARD (CEO) punya system.permission.manage via seeder, jadi route middleware lolos
        // Tapi GAP-27b: seharusnya hanya superadmin/IT yang bisa assign direct permission
        $this->actingAs($this->boardUser)
            ->post(route('users.assign-permission', $this->targetUser), [
                'permissions' => [$permission->id],
            ]);

        // GAP-27b: BOARD seharusnya TIDAK bisa assign direct permission
        // Saat ini lolos karena route middleware can:system.permission.manage dan CEO punya permission itu
        $this->assertFalse(
            $this->targetUser->fresh()->hasDirectPermission('organization.province.create'),
            'GAP-27b: BOARD (CEO) seharusnya tidak bisa assign direct permission — hanya superadmin/IT'
        );
    }

    // PC-17 — Assign permission oleh IT ke diri sendiri — BUG GAP-29
    public function test_assign_permission_superadmin_ke_diri_sendiri(): void
    {
        // Pilih permission yang Super Admin punya via role tapi belum direct
        $permission = Permission::where('name', 'system.webhook.manage')->first();

        $this->actingAs($this->superAdmin)
            ->post(route('users.assign-permission', $this->superAdmin), [
                'permissions' => [$permission->id],
            ]);

        // GAP-29: Superadmin seharusnya tidak bisa assign direct permission ke dirinya sendiri
        // Saat ini tidak ada self-lock guard, jadi syncPermissions() tetap jalan
        $this->assertFalse(
            $this->superAdmin->fresh()->hasDirectPermission('system.webhook.manage'),
            'GAP-29: Superadmin seharusnya tidak bisa assign permission ke dirinya sendiri'
        );
    }

    // PC-18 — Assign permission dengan permissions array kosong (revoke semua direct)
    public function test_assign_permission_permissions_array_kosong_revoke_semua(): void
    {
        $perm1 = Permission::where('name', 'organization.branch.create')->first();
        $perm2 = Permission::where('name', 'organization.area.create')->first();

        // Assign dulu 2 direct permissions
        $this->targetUser->syncPermissions([$perm1->id, $perm2->id]);
        $this->assertCount(2, $this->targetUser->fresh()->getDirectPermissions());

        // Revoke semua dengan array kosong
        $this->actingAs($this->superAdmin)
            ->post(route('users.assign-permission', $this->targetUser), [
                'permissions' => [],
            ])
            ->assertRedirect();

        $this->assertCount(0, $this->targetUser->fresh()->getDirectPermissions());
    }

    // Extra — Assign permission dengan id tidak exist
    public function test_assign_permission_permission_id_tidak_exist(): void
    {
        $this->actingAs($this->superAdmin)
            ->post(route('users.assign-permission', $this->targetUser), [
                'permissions' => [99999],
            ])
            ->assertRedirect()
            ->assertSessionHasErrors('permissions');
    }
}
