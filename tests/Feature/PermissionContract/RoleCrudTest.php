<?php

namespace Tests\Feature\PermissionContract;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;

    private User $unauthorizedUser;

    protected function setUp(): void
    {
        parent::setUp();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();


        

        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole('Super Admin');

        $this->unauthorizedUser = User::factory()->create();
        $this->unauthorizedUser->assignRole('REGULAR_TUTOR');
    }

    // PC-01 — Create role berhasil
    public function test_create_role_berhasil(): void
    {
        $this->actingAs($this->superAdmin)
            ->post(route('roles.store'), ['name' => 'New Role'])
            ->assertRedirect(route('roles.index'));

        $this->assertDatabaseHas('roles', ['name' => 'New Role']);
    }

    // PC-02 — Create role nama duplikat
    public function test_create_role_nama_duplikat(): void
    {
        Role::create(['name' => 'Existing Role', 'guard_name' => 'web']);

        $this->actingAs($this->superAdmin)
            ->post(route('roles.store'), ['name' => 'Existing Role'])
            ->assertRedirect()
            ->assertSessionHasErrors('name');
    }

    // PC-03 — Create role tanpa permission
    public function test_create_role_tanpa_permission(): void
    {
        $this->actingAs($this->unauthorizedUser)
            ->post(route('roles.store'), ['name' => 'Blocked Role'])
            ->assertStatus(403);
    }

    // PC-04 — Update role berhasil
    public function test_update_role_berhasil(): void
    {
        $role = Role::create(['name' => 'Old Name', 'guard_name' => 'web']);

        $this->actingAs($this->superAdmin)
            ->patch(route('roles.update', $role), ['name' => 'New Name'])
            ->assertRedirect(route('roles.index'));

        $this->assertDatabaseHas('roles', ['id' => $role->id, 'name' => 'New Name']);
    }

    // PC-05 — Update role nama sama (self)
    public function test_update_role_nama_sama_self(): void
    {
        $role = Role::create(['name' => 'Same Name', 'guard_name' => 'web']);

        $this->actingAs($this->superAdmin)
            ->patch(route('roles.update', $role), ['name' => 'Same Name'])
            ->assertRedirect(route('roles.index'));

        $this->assertDatabaseHas('roles', ['id' => $role->id, 'name' => 'Same Name']);
    }

    // PC-06 — Update role nama duplikat record lain
    public function test_update_role_nama_duplikat_record_lain(): void
    {
        Role::create(['name' => 'Taken Name', 'guard_name' => 'web']);
        $role = Role::create(['name' => 'My Role', 'guard_name' => 'web']);

        $this->actingAs($this->superAdmin)
            ->patch(route('roles.update', $role), ['name' => 'Taken Name'])
            ->assertRedirect()
            ->assertSessionHasErrors('name');
    }

    // PC-07 — Delete role masih punya user (BUG GAP-28)
    public function test_delete_role_masih_punya_user(): void
    {
        $role = Role::create(['name' => 'Role With Users', 'guard_name' => 'web']);
        $this->unauthorizedUser->assignRole($role);

        $this->actingAs($this->superAdmin)
            ->delete(route('roles.destroy', $role))
            ->assertRedirect();

        // GAP-28: Role seharusnya tidak bisa dihapus jika masih punya user
        // Saat ini destroy() langsung $role->delete() tanpa cek relasi users
        $stillExists = Role::where('id', $role->id)->exists();
        $this->assertTrue($stillExists,
            'GAP-28: Role seharusnya tidak bisa dihapus jika masih punya user terdaftar');
    }

    // PC-08 — Delete role tanpa permission
    public function test_delete_role_tanpa_permission(): void
    {
        $role = Role::create(['name' => 'Deletable Role', 'guard_name' => 'web']);

        $this->actingAs($this->unauthorizedUser)
            ->delete(route('roles.destroy', $role))
            ->assertStatus(403);
    }

    // Extra — Delete role berhasil (tanpa user)
    public function test_delete_role_tanpa_user_berhasil(): void
    {
        $role = Role::create(['name' => 'Empty Role', 'guard_name' => 'web']);

        $this->actingAs($this->superAdmin)
            ->delete(route('roles.destroy', $role))
            ->assertRedirect(route('roles.index'));

        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
    }
}
