<?php

namespace Tests\Feature\Auth;

use App\Models\MemberAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\Support\CreatesMember;
use Tests\TestCase;

class MemberLoginTest extends TestCase
{
    use CreatesMember;
    use RefreshDatabase;

    private MemberAccount $activeMember;

    private MemberAccount $inactiveMember;

    private MemberAccount $unverifiedMember;

    protected function setUp(): void
    {
        parent::setUp();

        $this->activeMember = $this->createMember([
            'email' => 'active.member@mindsia.test',
            'password' => 'Secret@123',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        $this->inactiveMember = $this->createMember([
            'email' => 'inactive.member@mindsia.test',
            'password' => 'Secret@123',
            'email_verified_at' => now(),
            'is_active' => false,
        ]);

        $this->unverifiedMember = $this->createMember([
            'email' => 'unverified.member@mindsia.test',
            'password' => 'Secret@123',
            'email_verified_at' => null,
            'is_active' => true,
        ]);
    }

    // ML-01 — Login berhasil, email verified, sudah bayar (active)
    public function test_login_berhasil_email_verified_active(): void
    {
        $this->post(route('member.login.post'), [
            'email' => 'active.member@mindsia.test',
            'password' => 'Secret@123',
        ])
            ->assertRedirect(route('member.dashboard'));

        $this->assertAuthenticated('member');
        $this->assertNotNull($this->activeMember->fresh()->last_login_at);
    }

    // ML-02 — Login berhasil, email verified, belum bayar (inactive)
    public function test_login_berhasil_email_verified_inactive(): void
    {
        $response = $this->post(route('member.login.post'), [
            'email' => 'inactive.member@mindsia.test',
            'password' => 'Secret@123',
        ]);

        $response->assertRedirect(route('member.dashboard'));
        $this->assertAuthenticated('member');
        $response->assertSessionHas('info', 'Akun sedang menunggu aktivasi admin.');
    }

    // ML-03 — Login, email belum verified
    public function test_login_email_belum_verified(): void
    {
        $this->post(route('member.login.post'), [
            'email' => 'unverified.member@mindsia.test',
            'password' => 'Secret@123',
        ])
            ->assertRedirect(route('member.dashboard'));

        $this->assertAuthenticated('member');

        // Setelah login, akses dashboard → middleware EnsureEmailIsVerified redirect ke verification.notice
        $this->get(route('member.dashboard'))
            ->assertRedirect(route('verification.notice'));
    }

    // ML-04 — Login, password salah
    public function test_login_password_salah(): void
    {
        $this->post(route('member.login.post'), [
            'email' => 'active.member@mindsia.test',
            'password' => 'WrongPassword!',
        ])
            ->assertRedirect()
            ->assertSessionHasErrors('email');

        $this->assertGuest('member');
    }

    // ML-05 — Login, email tidak terdaftar
    public function test_login_email_tidak_terdaftar(): void
    {
        $this->post(route('member.login.post'), [
            'email' => 'nonexistent@mindsia.test',
            'password' => 'Whatever@123',
        ])
            ->assertRedirect()
            ->assertSessionHasErrors('email');

        $this->assertGuest('member');
    }

    // ML-06 — Login, rate limit setelah 6x gagal
    public function test_login_rate_limit_6x_attempt(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('member.login.post'), [
                'email' => 'active.member@mindsia.test',
                'password' => 'WrongPass' . $i,
            ]);
        }

        // Attempt ke-6 seharusnya kena throttle
        $this->post(route('member.login.post'), [
            'email' => 'active.member@mindsia.test',
            'password' => 'WrongPass6',
        ])
            ->assertRedirect()
            ->assertSessionHasErrors('email');
    }

    // ML-07 — Login, rate limit reset setelah berhasil
    public function test_login_rate_limit_reset_setelah_berhasil(): void
    {
        // Gagal 4x
        for ($i = 0; $i < 4; $i++) {
            $this->post(route('member.login.post'), [
                'email' => 'active.member@mindsia.test',
                'password' => 'WrongPass' . $i,
            ]);
        }

        // Login berhasil — RateLimiter::clear() dipanggil
        $this->post(route('member.login.post'), [
            'email' => 'active.member@mindsia.test',
            'password' => 'Secret@123',
        ])
            ->assertRedirect(route('member.dashboard'));

        $this->assertAuthenticated('member');

        // Logout
        auth('member')->logout();

        // Harus bisa login lagi tanpa throttle (counter sudah di-clear)
        $this->post(route('member.login.post'), [
            'email' => 'active.member@mindsia.test',
            'password' => 'Secret@123',
        ])
            ->assertRedirect(route('member.dashboard'));
    }

    // ML-08 — Logout
    public function test_logout_invalidates_session(): void
    {
        $this->post(route('member.login.post'), [
            'email' => 'active.member@mindsia.test',
            'password' => 'Secret@123',
        ]);

        $this->assertAuthenticated('member');

        $this->post(route('member.logout'))
            ->assertRedirect('/');

        $this->assertGuest('member');
    }

    // ML-09 — Akses route protected tanpa login
    public function test_protected_route_tanpa_login_redirect_ke_member_login(): void
    {
        $this->get(route('member.dashboard'))
            ->assertRedirect(route('member.login'));
    }
}
