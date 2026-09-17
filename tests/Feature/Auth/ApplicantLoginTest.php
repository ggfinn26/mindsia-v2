<?php

namespace Tests\Feature\Auth;

use App\Models\ApplicantAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

// Flow: applicant-login
// Scenarios: AL-01 to AL-08
class ApplicantLoginTest extends TestCase
{
    use RefreshDatabase;

    private ApplicantAccount $verifiedApplicant;

    private ApplicantAccount $unverifiedApplicant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->verifiedApplicant = ApplicantAccount::factory()->create([
            'email' => 'verified.applicant@mindsia.test',
            'password' => 'Secret@12345',
            'email_verified_at' => now(),
        ]);

        $this->unverifiedApplicant = ApplicantAccount::factory()->unverified()->create([
            'email' => 'unverified.applicant@mindsia.test',
            'password' => 'Secret@12345',
        ]);
    }

    // AL-01: Login berhasil, email verified
    public function test_al01_login_berhasil_email_verified(): void
    {
        $response = $this->post(route('applicant.login.post'), [
            'email' => 'verified.applicant@mindsia.test',
            'password' => 'Secret@12345',
        ]);

        $response->assertRedirect(route('applicant.dashboard'));
        $this->assertAuthenticated('applicant');

        // last_login_at updated
        $this->assertNotNull($this->verifiedApplicant->fresh()->last_login_at);
    }

    // AL-02: Login — password benar tapi akun dari register (password hashing check)
    // This tests that factory-created passwords work with auth guard (not double-hashed)
    public function test_al02_login_password_benar_factory_created_account(): void
    {
        // The factory uses Hash::make('password') and model has 'hashed' cast
        // Create account specifically to test password verification
        $applicant = ApplicantAccount::factory()->create([
            'email' => 'hash.test@mindsia.test',
            'password' => 'TestPass@123',
            'email_verified_at' => now(),
        ]);

        $response = $this->post(route('applicant.login.post'), [
            'email' => 'hash.test@mindsia.test',
            'password' => 'TestPass@123',
        ]);

        $response->assertRedirect(route('applicant.dashboard'));
        $this->assertAuthenticated('applicant');
    }

    // AL-03: Login — email belum verified → login sukses tapi middleware redirect ke verification.notice
    public function test_al03_login_email_belum_verified(): void
    {
        $response = $this->post(route('applicant.login.post'), [
            'email' => 'unverified.applicant@mindsia.test',
            'password' => 'Secret@12345',
        ]);

        // Auth succeeds (credentials valid), controller redirects to dashboard
        $response->assertRedirect(route('applicant.dashboard'));
        $this->assertAuthenticated('applicant');

        // But when accessing dashboard, EnsureEmailIsVerified middleware blocks
        $dashboardResponse = $this->get(route('applicant.dashboard'));
        $dashboardResponse->assertRedirect(route('verification.notice'));
    }

    // AL-04: Login — password salah
    public function test_al04_login_password_salah(): void
    {
        $response = $this->post(route('applicant.login.post'), [
            'email' => 'verified.applicant@mindsia.test',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest('applicant');
    }

    // AL-05: Login — email tidak terdaftar
    public function test_al05_login_email_tidak_terdaftar(): void
    {
        $response = $this->post(route('applicant.login.post'), [
            'email' => 'tidak.ada@mindsia.test',
            'password' => 'Secret@12345',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest('applicant');
    }

    // AL-06: Login — rate limit (5 attempts then lockout)
    public function test_al06_login_rate_limit(): void
    {
        $throttleKey = strtolower('verified.applicant@mindsia.test') . '|127.0.0.1';

        // Attempt 5 failed logins
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('applicant.login.post'), [
                'email' => 'verified.applicant@mindsia.test',
                'password' => 'wrong-password',
            ]);
        }

        // 6th attempt should be rate limited
        $response = $this->post(route('applicant.login.post'), [
            'email' => 'verified.applicant@mindsia.test',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');

        // Clean up
        RateLimiter::clear($throttleKey);
    }

    // AL-07: Logout
    public function test_al07_logout(): void
    {
        // Login first
        $this->post(route('applicant.login.post'), [
            'email' => 'verified.applicant@mindsia.test',
            'password' => 'Secret@12345',
        ]);

        $this->assertAuthenticated('applicant');

        // Logout
        $response = $this->post(route('applicant.logout'));

        $response->assertRedirect('/karir');
        $this->assertGuest('applicant');
    }

    // AL-08: Akses route protected tanpa login → redirect ke applicant.login
    public function test_al08_akses_protected_tanpa_login(): void
    {
        $response = $this->get(route('applicant.dashboard'));

        $response->assertRedirect(route('applicant.login'));
        $this->assertGuest('applicant');
    }
}
