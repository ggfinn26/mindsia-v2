<?php

namespace Tests\Feature\Member;

use App\Models\Employee;
use App\Models\Institution;
use App\Models\MemberAccount;
use App\Models\MemberData;
use App\Models\Program;
use App\Models\User;
use App\Notifications\GuardedVerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

// Flow: register-member (RM-01 to RM-13)
// SKENARIO-TESTING.md: Admin-side member data creation, account creation, CSV import
class RegisterMemberTest extends TestCase
{
    use RefreshDatabase;

    private User $boardUser;

    private User $regularUser;

    private Institution $institution;

    protected function setUp(): void
    {
        parent::setUp();

        // BOARD_OF_DIRECTORS (CEO) bypasses all permission checks via Gate::before()
        $this->boardUser = User::factory()->create();
        $this->boardUser->assignRole('CEO');

        // Regular user with no member permissions
        $this->regularUser = User::factory()->create();
        $this->regularUser->assignRole('HRR');

        $this->institution = Institution::factory()->create();
    }

    private function validMemberData(array $overrides = []): array
    {
        return array_merge([
            'full_name' => 'Budi Santoso',
            'gender' => 'L',
            'birthdate' => '2000-01-15',
            'whatsapp_number' => '628123456789',
            'email' => 'budi@test.com',
            'institution_id' => $this->institution->id,
        ], $overrides);
    }

    // RM-01: create member data berhasil
    public function test_board_user_can_create_member_data(): void
    {
        $this->actingAs($this->boardUser)
            ->post('/members', $this->validMemberData())
            ->assertRedirect();

        $this->assertDatabaseHas('members_data', [
            'full_name' => 'Budi Santoso',
            'email' => 'budi@test.com',
        ]);
    }

    // RM-02: create member — email duplikat
    public function test_cannot_create_member_with_duplicate_email(): void
    {
        MemberData::factory()->create(['email' => 'budi@test.com', 'institution_id' => $this->institution->id]);

        $this->actingAs($this->boardUser)
            ->post('/members', $this->validMemberData())
            ->assertSessionHasErrors('email');
    }

    // RM-03: create member — institution_id tidak exist
    public function test_cannot_create_member_with_invalid_institution(): void
    {
        $this->actingAs($this->boardUser)
            ->post('/members', $this->validMemberData(['institution_id' => 99999]))
            ->assertSessionHasErrors('institution_id');
    }

    // RM-04: user tanpa permission member.manage → 403
    // StoreMemberDataRequest uses can('member.manage') (correct — GAP-140 is FALSE)
    // But member.manage NOT in PermissionSeeder → any non-BOARD user gets 403
    public function test_user_without_member_manage_permission_cannot_create_member(): void
    {
        $this->actingAs($this->regularUser)
            ->post('/members', $this->validMemberData())
            ->assertStatus(403);
    }

    // RM-04b: user WITH member.manage permission CAN create
    public function test_user_with_member_manage_permission_can_create_member(): void
    {
        $permission = Permission::firstOrCreate(['name' => 'member.manage', 'guard_name' => 'web']);
        $this->regularUser->givePermissionTo($permission);
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->actingAs($this->regularUser)
            ->post('/members', $this->validMemberData(['email' => 'budi2@test.com']))
            ->assertRedirect();

        $this->assertDatabaseHas('members_data', ['email' => 'budi2@test.com']);
    }

    // RM-05: member baru — activation_status saat store
    // DB column has DEFAULT 'pending_activation' → new member gets that even without explicit store
    // GAP-137 was wrong: field EXISTS and defaults to 'pending_activation'
    public function test_created_member_gets_pending_activation_status_by_db_default(): void
    {
        $this->actingAs($this->boardUser)
            ->post('/members', $this->validMemberData());

        $member = MemberData::where('email', 'budi@test.com')->first();
        $this->assertNotNull($member);
        // DB default kicks in → activation_status = 'pending_activation'
        $this->assertEquals('pending_activation', $member->activation_status);
    }

    // RM-05b: activate endpoint sets activation_status = 'active'
    public function test_activate_endpoint_sets_activation_status(): void
    {
        $member = MemberData::factory()->create([
            'institution_id' => $this->institution->id,
            'activation_status' => 'pending_activation',
        ]);

        $this->actingAs($this->boardUser)
            ->post("/members/{$member->id}/activate")
            ->assertRedirect();

        $this->assertDatabaseHas('members_data', [
            'id' => $member->id,
            'activation_status' => 'active',
        ]);
    }

    // RM-06: create member + buat akun login
    // GAP-138 skenario said endpoint doesn't handle MemberAccount — FALSE
    // Actual controller handles it via firstOrCreate
    public function test_can_create_member_with_account(): void
    {
        NotificationFacade::fake();

        $this->actingAs($this->boardUser)
            ->post('/members', $this->validMemberData([
                'create_account' => true,
            ]))
            ->assertRedirect();

        $member = MemberData::where('email', 'budi@test.com')->first();
        $this->assertNotNull($member);

        $account = MemberAccount::where('email', 'budi@test.com')->first();
        $this->assertNotNull($account);
        $this->assertEquals($member->id, $account->members_data_id);
    }

    // RM-07: email verifikasi dikirim setelah account dibuat
    // MemberAccount uses GuardedVerifyEmail via $account->notify()
    public function test_email_verification_sent_when_account_created(): void
    {
        NotificationFacade::fake();

        $this->actingAs($this->boardUser)
            ->post('/members', $this->validMemberData([
                'email' => 'verif@test.com',
                'create_account' => true,
            ]));

        $account = MemberAccount::where('email', 'verif@test.com')->first();
        $this->assertNotNull($account);

        NotificationFacade::assertSentTo($account, GuardedVerifyEmail::class);
    }

    // RM-08: delete member — tidak ada registrasi berhasil
    public function test_can_delete_member_without_registrations(): void
    {
        $member = MemberData::factory()->create(['institution_id' => $this->institution->id]);

        $this->actingAs($this->boardUser)
            ->delete("/members/{$member->id}")
            ->assertRedirect(route('members.index'));

        $this->assertDatabaseMissing('members_data', ['id' => $member->id]);
    }

    // RM-09: delete member — ada registrasi diblok
    // GAP: MemberDataRepository::delete() throws RuntimeException but controller doesn't catch it → 500
    // Should redirect back with error flash instead
    public function test_cannot_delete_member_with_registrations(): void
    {
        $member = MemberData::factory()->create(['institution_id' => $this->institution->id]);

        $program = Program::factory()->create();
        $employee = Employee::factory()->create();

        // Insert minimal registration directly (no factory for MemberRegistration)
        DB::table('members_registration')->insert([
            'members_data_id' => $member->id,
            'program_id' => $program->id,
            'employee_id' => $employee->id,
            'receipt_member_name' => $member->full_name,
            'receipt_institution_name' => 'Test Institution',
            'receipt_program_name' => 'Test Program',
            'original_price' => 1000000,
            'final_price' => 1000000,
            'graduation_status' => 'BELUM_LULUS',
            'payment_status' => 'unpaid',
            'installment_type' => '1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Fix Bug #95: controller sekarang catch RuntimeException → redirect back with error
        $this->actingAs($this->boardUser)
            ->delete("/members/{$member->id}")
            ->assertRedirect();

        $this->assertDatabaseHas('members_data', ['id' => $member->id]);
    }

    // RM-10: delete — destroy() uses abort_unless(can('member.manage')) — correct
    // GAP-141 said "tidak ada authorize()" — FALSE, code uses abort_unless properly
    public function test_user_without_permission_cannot_delete_member(): void
    {
        $member = MemberData::factory()->create(['institution_id' => $this->institution->id]);

        $this->actingAs($this->regularUser)
            ->delete("/members/{$member->id}")
            ->assertStatus(403);

        $this->assertDatabaseHas('members_data', ['id' => $member->id]);
    }

    // RM-11: CSV import berhasil
    // GAP-139 skenario said endpoint tidak ada — FALSE, import route EXISTS
    public function test_can_import_members_via_csv(): void
    {
        $csvContent = "full_name,email,gender,whatsapp_number,birthdate\n";
        $csvContent .= "Siti Rahayu,siti@test.com,P,628111222333,2001-05-20\n";

        $file = UploadedFile::fake()->createWithContent('members.csv', $csvContent);

        $this->actingAs($this->boardUser)
            ->post('/members/import', ['file' => $file])
            ->assertRedirect();

        $this->assertDatabaseHas('members_data', ['email' => 'siti@test.com']);
    }

    // RM-12: CSV import — row tanpa full_name → skip
    public function test_csv_import_skips_rows_without_full_name(): void
    {
        $csvContent = "full_name,email,gender,whatsapp_number,birthdate\n";
        $csvContent .= ",noemail@test.com,L,628999888777,2000-01-01\n";

        $file = UploadedFile::fake()->createWithContent('members.csv', $csvContent);

        $this->actingAs($this->boardUser)
            ->post('/members/import', ['file' => $file])
            ->assertRedirect();

        $this->assertDatabaseMissing('members_data', ['email' => 'noemail@test.com']);
    }

    // RM-13: CSV import — duplikat email → 500 (DB unique constraint violation)
    // members_data.email has unique index → MemberData::create() inside transaction throws PDOException
    // GAP: import should skip/notify on duplicate email, not crash with 500
    public function test_csv_import_with_duplicate_email_throws_500(): void
    {
        MemberData::factory()->create([
            'email' => 'sama@test.com',
            'institution_id' => $this->institution->id,
        ]);

        $csvContent = "full_name,email,gender,whatsapp_number\n";
        $csvContent .= "Nama Baru,sama@test.com,L,628111000111\n";

        $file = UploadedFile::fake()->createWithContent('members.csv', $csvContent);

        // ⚠️ GAP: unique constraint violation → 500 instead of graceful skip
        $this->actingAs($this->boardUser)
            ->post('/members/import', ['file' => $file])
            ->assertStatus(500);
    }
}
