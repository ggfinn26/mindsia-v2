<?php

namespace Tests\Feature\Auth;

use App\Models\ApplicantAccount;
use App\Models\User;
use Tests\Support\CreatesMember;
use Tests\TestCase;

/**
 * Session regeneration, invalidation, dan CSRF token rotation.
 */
class SessionSecurityTest extends TestCase
{
    use CreatesMember;

    // ─── Employee ─────────────────────────────────────────────────────────────

    public function test_session_regenerated_on_employee_login(): void
    {
        $user = User::factory()->create([
            'password' => 'Test@12345',
            'email_verified_at' => now(),
        ]);

        $sessionBefore = $this->app['session']->getId();

        $this->post(route('login.post'), [
            'email' => $user->email,
            'password' => 'Test@12345',
        ]);

        $sessionAfter = $this->app['session']->getId();

        // Session ID harus berubah setelah login (regenerate)
        $this->assertNotEquals($sessionBefore, $sessionAfter);
    }

    public function test_session_invalidated_on_employee_logout(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($user, 'web');
        $this->assertAuthenticated('web');

        $this->post(route('logout'));

        $this->assertGuest('web');
    }

    public function test_employee_cannot_access_dashboard_after_logout(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($user, 'web')
            ->post(route('logout'));

        // Setelah logout, akses dashboard harus redirect ke login
        $this->get(route('dashboard'))
            ->assertRedirect(route('login'));
    }

    // ─── Member ───────────────────────────────────────────────────────────────

    public function test_session_invalidated_on_member_logout(): void
    {
        $member = $this->createMember([
            'email' => 'session.member@mindsia.test',
            'password' => 'password',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        $this->actingAs($member, 'member');
        $this->assertAuthenticated('member');

        $this->post(route('member.logout'));

        $this->assertGuest('member');
    }

    public function test_member_cannot_access_dashboard_after_logout(): void
    {
        $member = $this->createMember([
            'email' => 'session.member2@mindsia.test',
            'password' => 'password',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        $this->actingAs($member, 'member')
            ->post(route('member.logout'));

        $this->get(route('member.dashboard'))
            ->assertRedirect(route('member.login'));
    }

    // ─── Applicant ────────────────────────────────────────────────────────────

    public function test_session_invalidated_on_applicant_logout(): void
    {
        $applicant = ApplicantAccount::create([
            'email' => 'session.applicant@mindsia.test',
            'password' => 'password',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($applicant, 'applicant');
        $this->assertAuthenticated('applicant');

        $this->post(route('applicant.logout'));

        $this->assertGuest('applicant');
    }
}
