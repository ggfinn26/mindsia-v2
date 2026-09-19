<?php

namespace Tests\Feature\Auth;

use App\Models\ApplicantAccount;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
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

    public function test_employee_email_verified_via_otp(): void
    {
        Event::fake();

        $user = User::factory()->unverified()->create();
        Cache::put("email_otp_web_{$user->id}", '123456', now()->addMinutes(15));

        $this->actingAs($user, 'web')
            ->post(route('verification.submit'), ['otp' => '123456'])
            ->assertRedirect(route('register.success'));

        $this->assertNotNull($user->fresh()->email_verified_at);
        Event::assertDispatched(Verified::class);
    }

    public function test_employee_email_verification_rejects_invalid_otp(): void
    {
        $user = User::factory()->unverified()->create();
        Cache::put("email_otp_web_{$user->id}", '123456', now()->addMinutes(15));

        $this->actingAs($user, 'web')
            ->post(route('verification.submit'), ['otp' => '999999'])
            ->assertSessionHasErrors('otp');

        $this->assertNull($user->fresh()->email_verified_at);
    }

    public function test_employee_otp_expired_rejected(): void
    {
        $user = User::factory()->unverified()->create();
        // no cache entry = expired

        $this->actingAs($user, 'web')
            ->post(route('verification.submit'), ['otp' => '123456'])
            ->assertSessionHasErrors('otp');

        $this->assertNull($user->fresh()->email_verified_at);
    }

    public function test_employee_otp_cannot_be_used_twice(): void
    {
        Event::fake();

        $user = User::factory()->unverified()->create();
        Cache::put("email_otp_web_{$user->id}", '123456', now()->addMinutes(5));

        // first submit — ok
        $this->actingAs($user, 'web')
            ->post(route('verification.submit'), ['otp' => '123456'])
            ->assertRedirect(route('register.success'));

        // second submit same OTP — cache already forgotten
        $this->actingAs($user->fresh(), 'web')
            ->post(route('verification.submit'), ['otp' => '123456'])
            ->assertRedirect(); // redirects away (already verified → dashboard)
    }

    public function test_otp_unauthenticated_with_session_resolves_account(): void
    {
        Event::fake();

        $user = User::factory()->unverified()->create();
        Cache::put("email_otp_web_{$user->id}", '654321', now()->addMinutes(5));

        $this->withSession(['pending_verification' => ['guard' => 'web', 'id' => $user->id]])
            ->post(route('verification.submit'), ['otp' => '654321'])
            ->assertRedirect(route('register.success'));

        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_otp_unauthenticated_without_session_redirects_login(): void
    {
        $this->post(route('verification.submit'), ['otp' => '123456'])
            ->assertRedirect(route('login'));
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
