<?php

namespace Tests\Feature\Auth;

use App\Models\ApplicantAccount;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Tests\Support\CreatesMember;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use CreatesMember;

    // ─── Employee ─────────────────────────────────────────────────────────────

    public function test_unverified_employee_redirected_to_verification_notice(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user, 'web')
            ->get(route('dashboard'))
            ->assertRedirect(route('verification.notice'));
    }

    public function test_verified_employee_can_access_dashboard(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($user, 'web')
            ->get(route('dashboard'))
            ->assertOk();
    }

    public function test_employee_email_verified_via_signed_url(): void
    {
        Event::fake();

        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email), 'guard' => 'web']
        );

        $this->actingAs($user, 'web')
            ->get($verificationUrl)
            ->assertRedirect(route('register.success'));

        $this->assertNotNull($user->fresh()->email_verified_at);
        Event::assertDispatched(Verified::class);
    }

    public function test_employee_email_verification_requires_valid_signature(): void
    {
        $user = User::factory()->unverified()->create();

        // URL tanpa signature yang valid
        $this->actingAs($user, 'web')
            ->get(route('verification.verify', [
                'id' => $user->id,
                'hash' => 'invalid-hash',
            ]))
            ->assertStatus(403);
    }

    public function test_employee_resend_verification_email(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user, 'web')
            ->post(route('verification.send'))
            ->assertRedirect();
    }

    // ─── Member ───────────────────────────────────────────────────────────────

    public function test_unverified_member_redirected_to_verification_notice(): void
    {
        $member = $this->createMember([
            'email' => 'unverified.member@mindsia.test',
            'password' => 'password',
            'email_verified_at' => null,
            'is_active' => false,
        ]);

        $this->actingAs($member, 'member')
            ->get(route('member.dashboard'))
            ->assertRedirect(route('verification.notice'));
    }

    public function test_verified_member_can_access_dashboard(): void
    {
        $member = $this->createMember([
            'email' => 'verified.member@mindsia.test',
            'password' => 'password',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        $this->actingAs($member, 'member')
            ->get(route('member.dashboard'))
            ->assertOk();
    }

    // ─── Applicant ────────────────────────────────────────────────────────────

    public function test_unverified_applicant_redirected_to_verification_notice(): void
    {
        $applicant = ApplicantAccount::create([
            'email' => 'unverified.applicant@mindsia.test',
            'password' => 'password',
            'email_verified_at' => null,
        ]);

        $this->actingAs($applicant, 'applicant')
            ->get(route('applicant.dashboard'))
            ->assertRedirect(route('verification.notice'));
    }
}
