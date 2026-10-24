<?php

namespace Tests\Feature\Auth;

use App\Models\ApplicantAccount;
use App\Models\MemberAccount;
use App\Models\User;
use Tests\Support\CreatesMember;
use Tests\TestCase;

/**
 * Memastikan session satu guard tidak bisa mengakses route guard lain.
 */
class GuardIsolationTest extends TestCase
{
    use CreatesMember;

    private User $employee;

    private MemberAccount $member;

    private ApplicantAccount $applicant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->employee = User::factory()->create(['email_verified_at' => now()]);

        $this->member = $this->createMember([
            'email' => 'guard.member@mindsia.test',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        $this->applicant = ApplicantAccount::create([
            'email' => 'guard.applicant@mindsia.test',
            'password' => 'password',
            'email_verified_at' => now(),
        ]);
    }

    // ─── Employee guard tidak bisa akses member/applicant routes ─────────────

    public function test_employee_session_cannot_access_member_dashboard(): void
    {
        $response = $this->actingAs($this->employee, 'web')
            ->get(route('member.dashboard'));

        // Harus redirect ke member login, bukan tampil dashboard
        $response->assertRedirect(route('member.login'));
    }

    public function test_employee_session_cannot_access_applicant_dashboard(): void
    {
        $response = $this->actingAs($this->employee, 'web')
            ->get(route('applicant.dashboard'));

        $response->assertRedirect(route('applicant.login'));
    }

    // ─── Member guard tidak bisa akses employee/applicant routes ─────────────

    public function test_member_session_cannot_access_employee_dashboard(): void
    {
        $response = $this->actingAs($this->member, 'member')
            ->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_member_session_cannot_access_applicant_dashboard(): void
    {
        $response = $this->actingAs($this->member, 'member')
            ->get(route('applicant.dashboard'));

        $response->assertRedirect(route('applicant.login'));
    }

    // ─── Applicant guard tidak bisa akses employee/member routes ─────────────

    public function test_applicant_session_cannot_access_employee_dashboard(): void
    {
        $response = $this->actingAs($this->applicant, 'applicant')
            ->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_applicant_session_cannot_access_member_dashboard(): void
    {
        $response = $this->actingAs($this->applicant, 'applicant')
            ->get(route('member.dashboard'));

        $response->assertRedirect(route('member.login'));
    }

    // ─── Unauthenticated redirect ke guard yang tepat ────────────────────────

    public function test_unauthenticated_employee_route_redirects_to_employee_login(): void
    {
        $this->get(route('dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_unauthenticated_member_route_redirects_to_member_login(): void
    {
        $this->get(route('member.dashboard'))
            ->assertRedirect(route('member.login'));
    }

    public function test_unauthenticated_applicant_route_redirects_to_applicant_login(): void
    {
        $this->get(route('applicant.dashboard'))
            ->assertRedirect(route('applicant.login'));
    }

    // ─── Login satu guard harus mematikan session guard lain (single identity) ─

    public function test_member_login_terminates_active_employee_session(): void
    {
        $this->actingAs($this->employee, 'web');
        $this->assertAuthenticated('web');

        $this->post(route('member.login.post'), [
            'email' => 'guard.member@mindsia.test',
            'password' => 'password',
        ])->assertRedirect(route('member.dashboard'));

        $this->assertAuthenticated('member');
        $this->assertGuest('web');
    }

    public function test_employee_login_terminates_active_member_session(): void
    {
        $this->actingAs($this->member, 'member');
        $this->assertAuthenticated('member');

        $this->post(route('login.post'), [
            'email' => $this->employee->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticated('web');
        $this->assertGuest('member');
    }

    public function test_applicant_login_terminates_other_sessions(): void
    {
        $this->actingAs($this->employee, 'web');
        $this->actingAs($this->member, 'member');

        $this->post(route('applicant.login.post'), [
            'email' => 'guard.applicant@mindsia.test',
            'password' => 'password',
        ])->assertRedirect(route('applicant.dashboard'));

        $this->assertAuthenticated('applicant');
        $this->assertGuest('web');
        $this->assertGuest('member');
    }
}
