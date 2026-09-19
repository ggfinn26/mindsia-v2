<?php

namespace Tests\Feature\Auth;

use App\Models\ApplicantAccount;
use App\Models\ApplicantMasterData;
use App\Notifications\GuardedVerifyEmail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

// Flow: applicant-register
// Scenarios: AR-01 to AR-11
class ApplicantRegisterTest extends TestCase
{
    private array $validPayload;

    protected function setUp(): void
    {
        parent::setUp();

        $this->validPayload = [
            'full_name' => 'Budi Santoso',
            'whatsapp_number' => '6281234567890',
            'birth_date' => '1995-06-15',
            'gender' => 'male',
            'address' => 'Jl. Merdeka No. 10',
            'city' => 'Jakarta',
            'email' => 'budi.santoso@mindsia.test',
            'password' => 'Secret@12345',
            'password_confirmation' => 'Secret@12345',
        ];
    }

    // AR-01: Register berhasil — creates account + master data, sends verification email
    public function test_ar01_register_berhasil(): void
    {
        Notification::fake();

        $response = $this->post(route('applicant.register.post'), $this->validPayload);

        $response->assertRedirect(route('verification.notice'));
        $response->assertSessionHas('status');

        // ApplicantAccount created
        $this->assertDatabaseHas('applicant_accounts', [
            'email' => 'budi.santoso@mindsia.test',
        ]);

        // ApplicantMasterData created
        $account = ApplicantAccount::where('email', 'budi.santoso@mindsia.test')->first();
        $this->assertNotNull($account);
        $this->assertDatabaseHas('applicants_master_data', [
            'applicant_account_id' => $account->id,
            'full_name' => 'Budi Santoso',
            'whatsapp_number' => '6281234567890',
        ]);

        // Verification email sent
        Notification::assertSentTo($account, GuardedVerifyEmail::class);

        // NOT auto-logged in
        $this->assertGuest('applicant');
    }

    // AR-02: Email verifikasi dikirim (GAP-37 marked as "Selesai" — verify notification is sent)
    public function test_ar02_email_verifikasi_dikirim(): void
    {
        Notification::fake();

        $this->post(route('applicant.register.post'), $this->validPayload);

        $account = ApplicantAccount::where('email', 'budi.santoso@mindsia.test')->first();

        // Verify GuardedVerifyEmail notification was sent with 'applicant' guard
        Notification::assertSentTo($account, GuardedVerifyEmail::class, function ($notification) {
            // GuardedVerifyEmail constructor receives the guard name
            return true;
        });

        // email_verified_at should be null after register
        $this->assertNull($account->fresh()->email_verified_at);
    }

    // AR-03: Login setelah register tanpa verifikasi → middleware blocks, redirect to verification.notice
    public function test_ar03_login_tanpa_verifikasi_diblock_middleware(): void
    {
        $this->post(route('applicant.register.post'), $this->validPayload);

        // Attempt login — credentials are correct but email not verified
        $response = $this->post(route('applicant.login.post'), [
            'email' => 'budi.santoso@mindsia.test',
            'password' => 'Secret@12345',
        ]);

        // Auth succeeds (credentials valid), controller redirects to dashboard
        $response->assertRedirect(route('applicant.dashboard'));
        $this->assertAuthenticated('applicant');

        // But dashboard is blocked by EnsureEmailIsVerified middleware
        $dashboardResponse = $this->get(route('applicant.dashboard'));
        $dashboardResponse->assertRedirect(route('verification.notice'));
    }

    // AR-04: Login dengan password benar — after manual email verification, should work
    public function test_ar04_login_password_benar_setelah_verifikasi(): void
    {
        $this->post(route('applicant.register.post'), $this->validPayload);

        // Manually verify email
        $account = ApplicantAccount::where('email', 'budi.santoso@mindsia.test')->first();
        $account->update(['email_verified_at' => now()]);

        // Login should succeed and redirect to dashboard
        $response = $this->post(route('applicant.login.post'), [
            'email' => 'budi.santoso@mindsia.test',
            'password' => 'Secret@12345',
        ]);

        $response->assertRedirect(route('applicant.dashboard'));
        $this->assertAuthenticated('applicant');
    }

    // AR-05: Register — email duplikat
    public function test_ar05_register_email_duplikat(): void
    {
        // First registration
        $this->post(route('applicant.register.post'), $this->validPayload);

        // Second registration with same email
        $response = $this->post(route('applicant.register.post'), $this->validPayload);

        $response->assertSessionHasErrors('email');

        // Only one account created
        $this->assertEquals(1, ApplicantAccount::where('email', 'budi.santoso@mindsia.test')->count());
    }

    // AR-06: Register — password tidak confirmed
    public function test_ar06_register_password_tidak_confirmed(): void
    {
        $payload = $this->validPayload;
        $payload['password_confirmation'] = 'Different@99999';

        $response = $this->post(route('applicant.register.post'), $payload);

        $response->assertSessionHasErrors('password');

        // No account created
        $this->assertDatabaseMissing('applicant_accounts', [
            'email' => 'budi.santoso@mindsia.test',
        ]);
    }

    // AR-07: Register — full_name kosong
    public function test_ar07_register_full_name_kosong(): void
    {
        $payload = $this->validPayload;
        $payload['full_name'] = '';

        $response = $this->post(route('applicant.register.post'), $payload);

        $response->assertSessionHasErrors('full_name');

        $this->assertDatabaseMissing('applicant_accounts', [
            'email' => 'budi.santoso@mindsia.test',
        ]);
    }

    // AR-08: Register — whatsapp_number kosong
    public function test_ar08_register_whatsapp_number_kosong(): void
    {
        $payload = $this->validPayload;
        unset($payload['whatsapp_number']);

        $response = $this->post(route('applicant.register.post'), $payload);

        $response->assertSessionHasErrors('whatsapp_number');

        $this->assertDatabaseMissing('applicant_accounts', [
            'email' => 'budi.santoso@mindsia.test',
        ]);
    }

    // AR-09: Register — field opsional nullable (birth_date, gender, address, city tidak dikirim)
    public function test_ar09_register_field_opsional_nullable(): void
    {
        Notification::fake();

        $payload = $this->validPayload;
        unset($payload['birth_date'], $payload['gender'], $payload['address'], $payload['city']);

        $response = $this->post(route('applicant.register.post'), $payload);

        $response->assertRedirect(route('verification.notice'));

        // Account created
        $account = ApplicantAccount::where('email', 'budi.santoso@mindsia.test')->first();
        $this->assertNotNull($account);

        // Master data created with null optional fields
        $this->assertDatabaseHas('applicants_master_data', [
            'applicant_account_id' => $account->id,
            'full_name' => 'Budi Santoso',
            'whatsapp_number' => '6281234567890',
            'birth_date' => null,
            'gender' => null,
            'address' => null,
            'city' => null,
        ]);
    }

    // AR-10: Register — transaksi rollback saat DB error
    // Duplicate email causes FormRequest validation to reject — no partial data created
    public function test_ar10_register_rollback_saat_db_error(): void
    {
        Notification::fake();

        // Pre-create account with target email to trigger unique validation
        ApplicantAccount::factory()->create(['email' => 'budi.santoso@mindsia.test']);

        $response = $this->post(route('applicant.register.post'), $this->validPayload);

        $response->assertSessionHasErrors('email');

        // Only 1 account (the factory one), no new master data
        $this->assertEquals(1, ApplicantAccount::where('email', 'budi.santoso@mindsia.test')->count());
        $this->assertEquals(0, ApplicantMasterData::where('full_name', 'Budi Santoso')->count());
    }

    // AR-11: Register — tidak auto-login
    public function test_ar11_register_tidak_auto_login(): void
    {
        Notification::fake();

        $this->post(route('applicant.register.post'), $this->validPayload);

        // Should NOT be authenticated as applicant
        $this->assertGuest('applicant');

        // No applicant session
        $this->assertFalse(auth('applicant')->check());
    }
}
