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
}
