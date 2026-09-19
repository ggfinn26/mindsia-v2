<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class EmployeeLoginTest extends TestCase
{
    private User $verifiedUser;

    private User $unverifiedUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->verifiedUser = User::factory()->create([
            'email' => 'verified@mindsia.test',
            'password' => 'Test@12345',
            'email_verified_at' => now(),
        ]);

        $this->unverifiedUser = User::factory()->unverified()->create([
            'email' => 'unverified@mindsia.test',
            'password' => 'Test@12345',
        ]);
    }

    // EL-01
    public function test_login_berhasil_redirect_dashboard(): void
    {
        $this->post(route('login.post'), [
            'email' => 'verified@mindsia.test',
            'password' => 'Test@12345',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticated('web');
    }

    // EL-02
    public function test_login_password_salah_returns_422_error(): void
    {
        $this->post(route('login.post'), [
            'email' => 'verified@mindsia.test',
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest('web');
    }

    // EL-03
    public function test_login_email_tidak_terdaftar_returns_error_generik(): void
    {
        $this->post(route('login.post'), [
            'email' => 'tidak.ada@mindsia.test',
            'password' => 'Test@12345',
        ])->assertSessionHasErrors('email');

        $this->assertGuest('web');
    }

    // EL-04
    public function test_login_email_belum_verified_redirect_verification_notice(): void
    {
        $this->post(route('login.post'), [
            'email' => 'unverified@mindsia.test',
            'password' => 'Test@12345',
        ])->assertRedirect(route('verification.notice'));
    }

    // EL-05
    public function test_login_rate_limit_after_6_failed_attempts(): void
    {
        // Throttle key: {email}|{ip} — sesuai LoginRequest::throttleKey()
        $throttleKey = 'verified@mindsia.test|127.0.0.1';
        RateLimiter::clear($throttleKey);

        for ($i = 0; $i < 6; $i++) {
            $this->post(route('login.post'), [
                'email' => 'verified@mindsia.test',
                'password' => 'wrong',
            ]);
        }

        $response = $this->post(route('login.post'), [
            'email' => 'verified@mindsia.test',
            'password' => 'wrong',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertStringContainsString('Terlalu banyak', session('errors')->first('email'));
    }

    // EL-07
    public function test_logout_invalidates_session(): void
    {
        $this->actingAs($this->verifiedUser, 'web');
        $this->assertAuthenticated('web');

        $this->post(route('logout'));

        $this->assertGuest('web');
    }

    // EL-07 — redirect ke /
    public function test_logout_redirects_to_root(): void
    {
        $this->actingAs($this->verifiedUser, 'web')
            ->post(route('logout'))
            ->assertRedirect('/');
    }

    // EL-09
    public function test_protected_route_tanpa_login_redirect_ke_login(): void
    {
        $this->get(route('dashboard'))
            ->assertRedirect(route('login'));
    }

    // EL-08 — last_login_at diupdate (GAP-23 — expected fail jika belum difix)
    public function test_last_login_at_diupdate_saat_login(): void
    {
        $before = now()->subSecond();

        $this->post(route('login.post'), [
            'email' => 'verified@mindsia.test',
            'password' => 'Test@12345',
        ]);

        $this->assertGreaterThanOrEqual(
            $before,
            $this->verifiedUser->fresh()->last_login_at,
            'last_login_at harus diupdate setelah login [GAP-23]'
        );
    }
}
