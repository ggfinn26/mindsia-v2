<?php

namespace Tests\Feature\Auth;

use App\Models\ApplicantAccount;
use App\Models\MemberAccount;
use App\Models\MemberData;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Memverifikasi bahwa password selalu disimpan sebagai hash tunggal.
 * Dua sumber double-hash yang ditemukan:
 *   1. UserFactory (sudah difix — Hash::make dihapus)
 *   2. UserManagementController::reset (sudah difix — Hash::make dihapus)
 */
class PasswordHashTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_password_stored_as_single_hash(): void
    {
        $user = User::factory()->create(['password' => 'raw-password-123']);

        // Hash::check harus berhasil — kalau double-hash, ini akan false
        $this->assertTrue(Hash::check('raw-password-123', $user->fresh()->password));
    }

    public function test_user_factory_default_password_is_checkable(): void
    {
        $user = User::factory()->create();

        // Factory default password adalah 'password'
        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    public function test_user_password_not_stored_as_plaintext(): void
    {
        $user = User::factory()->create(['password' => 'secret123']);

        $this->assertNotEquals('secret123', $user->fresh()->password);
    }

    public function test_member_account_password_stored_as_single_hash(): void
    {
        $memberData = MemberData::create([
            'full_name' => 'Test Member Hash',
            'email' => 'hash.test@mindsia.test',
            'whatsapp_number' => '08000000001',
        ]);

        $member = MemberAccount::create([
            'members_data_id' => $memberData->id,
            'email' => 'hash.test@mindsia.test',
            'password' => 'raw-password-123',
        ]);

        $this->assertTrue(Hash::check('raw-password-123', $member->fresh()->password));
    }

    public function test_applicant_account_password_stored_as_single_hash(): void
    {
        $applicant = ApplicantAccount::create([
            'email' => 'hash.applicant@mindsia.test',
            'password' => 'raw-password-123',
        ]);

        $this->assertTrue(Hash::check('raw-password-123', $applicant->fresh()->password));
    }

    public function test_change_password_stores_single_hash(): void
    {
        $user = User::factory()->create(['password' => 'OldPass@123']);

        $user->update(['password' => 'NewPass@456']);

        $this->assertTrue(Hash::check('NewPass@456', $user->fresh()->password));
        $this->assertFalse(Hash::check('OldPass@123', $user->fresh()->password));
    }

    public function test_admin_reset_stores_single_hash(): void
    {
        

        $admin = User::factory()->create(['password' => 'AdminPass@123']);
        $admin->givePermissionTo('auth.user.force_reset_password');

        $target = User::factory()->create(['password' => 'OldPass@123']);

        $this->actingAs($admin)
            ->post(route('users.reset-password.update', $target), [
                'current_password' => 'AdminPass@123',
                'password' => 'NewAdmin@456',
            ]);

        $this->assertTrue(Hash::check('NewAdmin@456', $target->fresh()->password));
    }
}
