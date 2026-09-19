<?php

namespace Tests\Feature\Auth;

use App\Models\ApplicantAccount;
use App\Models\User;
use App\Notifications\MemberResetPasswordNotification;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Spatie\Permission\Models\Permission;
use Tests\Support\CreatesMember;
use Tests\TestCase;

/**
 * Ganti password (3 guard) + forgot/reset flow.
 * GAP-43: double hash di PasswordController — SUDAH DIFIX (UserFactory fix).
 * GAP-44: double hash di UserManagementController — SUDAH DIFIX.
 */
class PasswordManagementTest extends TestCase
{
    use CreatesMember;

    protected function setUp(): void
    {
        parent::setUp();
        Permission::firstOrCreate(['name' => 'auth.user.force_reset_password', 'guard_name' => 'web']);
    }

    // ─── Employee: ganti password sendiri ─────────────────────────────────────

    public function test_employee_change_password_berhasil(): void
    {
        $user = User::factory()->create([
            'password' => 'OldPass@123',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user, 'web')
            ->post(route('password.change.post'), [
                'current_password' => 'OldPass@123',
                'password' => 'NewPass@456',
                'password_confirmation' => 'NewPass@456',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        // Verifikasi password baru bisa digunakan (GAP-43 test)
        $this->assertTrue(Hash::check('NewPass@456', $user->fresh()->password));
    }

    public function test_employee_change_password_current_salah_returns_error(): void
    {
        $user = User::factory()->create([
            'password' => 'OldPass@123',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user, 'web')
            ->post(route('password.change.post'), [
                'current_password' => 'WrongPass@999',
                'password' => 'NewPass@456',
                'password_confirmation' => 'NewPass@456',
            ])
            ->assertSessionHasErrors('current_password');
    }

    public function test_employee_change_password_sama_dengan_lama_returns_error(): void
    {
        $user = User::factory()->create([
            'password' => 'SamePass@123',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user, 'web')
            ->post(route('password.change.post'), [
                'current_password' => 'SamePass@123',
                'password' => 'SamePass@123',
                'password_confirmation' => 'SamePass@123',
            ])
            ->assertSessionHasErrors('password');
    }

    public function test_employee_change_password_tanpa_login_redirect(): void
    {
        $this->post(route('password.change.post'), [
            'current_password' => 'OldPass@123',
            'password' => 'NewPass@456',
            'password_confirmation' => 'NewPass@456',
        ])->assertRedirect(route('login'));
    }

    // ─── Employee: forgot/reset password ──────────────────────────────────────

    public function test_employee_forgot_password_kirim_link(): void
    {
        Notification::fake();

        $user = User::factory()->create(['email_verified_at' => now()]);

        $this->post(route('password.email'), ['email' => $user->email])
            ->assertSessionHasNoErrors();

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_employee_forgot_password_email_tidak_ada_returns_error(): void
    {
        $this->post(route('password.email'), ['email' => 'tidak.ada@mindsia.test'])
            ->assertSessionHasErrors('email');
    }

    public function test_employee_reset_password_berhasil_login_dengan_password_baru(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        // Generate token langsung via Password broker
        $token = Password::createToken($user);

        $this->post(route('password.update'), [
            'email' => $user->email,
            'password' => 'ResetNew@789',
            'password_confirmation' => 'ResetNew@789',
            'token' => $token,
        ])->assertRedirect(route('login'));

        // Password baru harus bisa digunakan (GAP-44 test)
        $this->assertTrue(Hash::check('ResetNew@789', $user->fresh()->password));
    }

    public function test_employee_reset_password_token_invalid_returns_error(): void
    {
        $user = User::factory()->create();

        $this->post(route('password.update'), [
            'email' => $user->email,
            'password' => 'NewPass@456',
            'password_confirmation' => 'NewPass@456',
            'token' => 'invalid-token-xyz',
        ])->assertSessionHasErrors('email');
    }

    // ─── Admin force reset ─────────────────────────────────────────────────────

    public function test_admin_force_reset_berhasil_password_baru_bisa_digunakan(): void
    {

        $admin = User::factory()->create([
            'password' => 'AdminPass@123',
            'email_verified_at' => now(),
        ]);
        $admin->givePermissionTo('auth.user.force_reset_password');

        $target = User::factory()->create([
            'password' => 'OldPass@123',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($admin, 'web')
            ->post(route('users.reset-password.update', $target), [
                'current_password' => 'AdminPass@123',
                'password' => 'ForcedNew@789',
                'password_confirmation' => 'ForcedNew@789',
            ])
            ->assertSessionHasNoErrors();

        $this->assertTrue(Hash::check('ForcedNew@789', $target->fresh()->password));
        $this->assertTrue($target->fresh()->must_change_password);
    }

    public function test_admin_force_reset_current_password_salah_returns_error(): void
    {

        $admin = User::factory()->create([
            'password' => 'AdminPass@123',
            'email_verified_at' => now(),
        ]);
        $admin->givePermissionTo('auth.user.force_reset_password');

        $target = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($admin, 'web')
            ->post(route('users.reset-password.update', $target), [
                'current_password' => 'WrongAdmin@999',
                'password' => 'ForcedNew@789',
                'password_confirmation' => 'ForcedNew@789',
            ])
            ->assertSessionHasErrors('current_password');
    }

    public function test_admin_force_reset_tanpa_permission_returns_403(): void
    {

        $nonAdmin = User::factory()->create(['email_verified_at' => now()]);
        $target = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($nonAdmin, 'web')
            ->post(route('users.reset-password.update', $target), [
                'current_password' => 'password',
                'password' => 'ForcedNew@789',
                'password_confirmation' => 'ForcedNew@789',
            ])
            ->assertForbidden();
    }

    // ─── Member: ganti password ────────────────────────────────────────────────

    public function test_member_change_password_berhasil(): void
    {
        $member = $this->createMember([
            'email' => 'member.pass@mindsia.test',
            'password' => 'OldPass@123',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        $this->actingAs($member, 'member')
            ->post(route('member.password.change.post'), [
                'current_password' => 'OldPass@123',
                'password' => 'MemberNew@456',
                'password_confirmation' => 'MemberNew@456',
            ])
            ->assertSessionHasNoErrors();

        $this->assertTrue(Hash::check('MemberNew@456', $member->fresh()->password));
    }

    public function test_member_forgot_password_kirim_link(): void
    {
        Notification::fake();

        $member = $this->createMember([
            'email' => 'forgot.member@mindsia.test',
            'password' => 'password',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        $this->post(route('member.password.email'), ['email' => $member->email])
            ->assertSessionHasNoErrors();

        Notification::assertSentTo($member, MemberResetPasswordNotification::class);
    }

    // ─── Applicant: ganti password ─────────────────────────────────────────────

    public function test_applicant_change_password_berhasil(): void
    {
        $applicant = ApplicantAccount::create([
            'email' => 'applicant.pass@mindsia.test',
            'password' => 'OldPass@123',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($applicant, 'applicant')
            ->post(route('applicant.password.change.post'), [
                'current_password' => 'OldPass@123',
                'password' => 'ApplicantNew@456',
                'password_confirmation' => 'ApplicantNew@456',
            ])
            ->assertSessionHasNoErrors();

        $this->assertTrue(Hash::check('ApplicantNew@456', $applicant->fresh()->password));
    }
}
