<?php

namespace Tests\Feature\Auth;

use App\Models\Employee;
use App\Models\Institution;
use App\Models\MemberAccount;
use App\Models\MemberData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\Support\CreatesMember;
use Tests\TestCase;

class MemberSelfRegisterTest extends TestCase
{
    use CreatesMember;
    use RefreshDatabase;

    private Institution $institution;

    protected function setUp(): void
    {
        parent::setUp();

        $this->institution = Institution::factory()->create();
    }

    /** Payload valid untuk register — tanpa referral & program_id */
    private function validPayload(array $override = []): array
    {
        return array_merge([
            'full_name' => 'Budi Santoso',
            'gender' => 'L',
            'birthdate' => '2000-01-15',
            'whatsapp_number' => '081234567890',
            'email' => 'budi.' . uniqid() . '@mindsia.test',
            'instagram' => 'budi_santoso',
            'father_name' => 'Ayah Budi',
            'mother_name' => 'Ibu Budi',
            'father_occupation' => 'PNS',
            'mother_occupation' => 'IRT',
            'father_whatsapp' => '081299988877',
            'mother_whatsapp' => '081299988876',
            'address' => 'Jl. Merdeka No. 10, Jakarta',
            'institution_id' => $this->institution->id,
            'program_id' => null,
            'referred_by_code' => null,
            'password' => 'Secret@123',
            'password_confirmation' => 'Secret@123',
        ], $override);
    }

    // MR-01 — Register berhasil tanpa referral
    public function test_register_berhasil_tanpa_referral(): void
    {
        Notification::fake();

        $payload = $this->validPayload();
        $email = $payload['email'];

        $this->post(route('member.register.post'), $payload)
            ->assertRedirect(route('verification.notice'));

        // MemberData dibuat
        $this->assertDatabaseHas('members_data', [
            'full_name' => 'Budi Santoso',
            'email' => $email,
            'institution_id' => $this->institution->id,
        ]);

        // MemberAccount dibuat dengan email yang sama
        $memberData = MemberData::where('email', $email)->first();
        $this->assertNotNull($memberData);
        $this->assertDatabaseHas('member_accounts', [
            'members_data_id' => $memberData->id,
            'email' => $email,
        ]);

        // Email verifikasi terkirim
        $member = MemberAccount::where('email', $email)->first();
        Notification::assertSentTo($member, \App\Notifications\GuardedVerifyEmail::class);
    }

    // MR-02 — Register berhasil dengan referral valid
    public function test_register_berhasil_dengan_referral_valid(): void
    {
        Notification::fake();

        $employee = Employee::factory()->create(['employee_code' => 'REF001']);

        $payload = $this->validPayload(['referred_by_code' => 'REF001']);

        $this->post(route('member.register.post'), $payload)
            ->assertRedirect(route('verification.notice'));

        $memberData = MemberData::where('email', $payload['email'])->first();
        $this->assertNotNull($memberData);
        $this->assertEquals($employee->id, $memberData->referred_by_employee_id);
    }

    // MR-03 — Referral code tidak ditemukan
    public function test_register_referral_code_tidak_ditemukan(): void
    {
        $payload = $this->validPayload(['referred_by_code' => 'NOTEXIST']);

        $this->post(route('member.register.post'), $payload)
            ->assertRedirect()
            ->assertSessionHasErrors('referred_by_code');

        // Tidak ada data yang dibuat
        $this->assertDatabaseEmpty('member_accounts');
        $this->assertEquals(0, MemberData::count());
    }

    // MR-04 — Email duplikat
    public function test_register_email_duplikat(): void
    {
        $existingEmail = 'duplikat@mindsia.test';
        $this->createMember(['email' => $existingEmail], ['email' => $existingEmail]);

        $payload = $this->validPayload(['email' => $existingEmail]);

        $this->post(route('member.register.post'), $payload)
            ->assertRedirect()
            ->assertSessionHasErrors('email');
    }

    // MR-05 — Gender tidak valid
    public function test_register_gender_tidak_valid(): void
    {
        $payload = $this->validPayload(['gender' => 'X']);

        $this->post(route('member.register.post'), $payload)
            ->assertRedirect()
            ->assertSessionHasErrors('gender');
    }

    // MR-06 — institution_id tidak exist
    public function test_register_institution_id_tidak_exist(): void
    {
        $payload = $this->validPayload(['institution_id' => 99999]);

        $this->post(route('member.register.post'), $payload)
            ->assertRedirect()
            ->assertSessionHasErrors('institution_id');
    }

    // MR-07 — Password tidak confirmed
    public function test_register_password_tidak_confirmed(): void
    {
        $payload = $this->validPayload([
            'password' => 'Secret@123',
            'password_confirmation' => 'Different@456',
        ]);

        $this->post(route('member.register.post'), $payload)
            ->assertRedirect()
            ->assertSessionHasErrors('password');
    }

    // MR-08 — MemberAccount.is_active default false setelah register
    public function test_register_member_account_is_active_default_false(): void
    {
        Notification::fake();

        $payload = $this->validPayload();

        $this->post(route('member.register.post'), $payload)
            ->assertRedirect(route('verification.notice'));

        $member = MemberAccount::where('email', $payload['email'])->first();
        $this->assertNotNull($member);
        $this->assertFalse($member->is_active);
    }

    // MR-09 — Tidak auto-login setelah register
    public function test_register_tidak_auto_login(): void
    {
        Notification::fake();

        $payload = $this->validPayload();

        $this->post(route('member.register.post'), $payload)
            ->assertRedirect(route('verification.notice'));

        $this->assertGuest('member');
    }

    // MR-10 — program_id nullable
    public function test_register_program_id_nullable(): void
    {
        Notification::fake();

        // Tidak kirim program_id sama sekali
        $payload = $this->validPayload();
        unset($payload['program_id']);

        $this->post(route('member.register.post'), $payload)
            ->assertRedirect(route('verification.notice'));

        $memberData = MemberData::where('email', $payload['email'])->first();
        $this->assertNotNull($memberData);
        $this->assertNull($memberData->program_id);
    }

    // MR-11 — Transaksi rollback jika gagal saat MemberAccount::create
    public function test_register_rollback_jika_member_account_gagal(): void
    {
        $payload = $this->validPayload();

        // Count sebelum attempt
        $memberDataCountBefore = MemberData::count();
        $memberAccountCountBefore = MemberAccount::count();

        // Force DB error saat MemberAccount::create via model event
        MemberAccount::creating(function () {
            throw new \Exception('Simulated DB error');
        });

        $this->post(route('member.register.post'), $payload)
            ->assertRedirect()
            ->assertSessionHasErrors('registration');

        // MemberData TIDAK tersimpan (rollback oleh DB::transaction)
        $this->assertEquals($memberDataCountBefore, MemberData::count());
        $this->assertEquals($memberAccountCountBefore, MemberAccount::count());

        // Cleanup model events
        MemberAccount::flushEventListeners();
    }
}
