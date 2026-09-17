<?php

namespace Tests\Feature\Member;

use App\Models\Branch;
use App\Models\MemberAccount;
use App\Models\MemberData;
use App\Models\MemberReview;
use App\Models\Program;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\Support\CreatesMember;
use Tests\TestCase;

// Flow: member-review-nps (NR-01 to NR-11)
// SKENARIO-TESTING.md: NPS submit member portal, admin manage review, approve/reject
class MemberReviewNpsTest extends TestCase
{
    use CreatesMember;
    use RefreshDatabase;

    private MemberAccount $memberAccount;

    private MemberData $memberData;

    private Branch $branch;

    private User $boardUser;

    private int $employeeId;

    protected function setUp(): void
    {
        parent::setUp();

        $uid = uniqid();
        $this->employeeId = DB::table('employees')->insertGetId([
            'employee_code' => "EMP-NR-{$uid}",
            'full_name' => 'NPS Admin',
            'gender' => 'L',
            'birthdate' => '1990-01-01',
            'email' => "emp.nr.{$uid}@test.com",
            'whatsapp_number' => '628111222333',
            'is_hq' => true,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->boardUser = User::factory()->create(['employee_id' => $this->employeeId]);
        $this->boardUser->assignRole('CEO');

        $this->branch = Branch::factory()->create();
        $this->memberAccount = $this->createMember();
        $this->memberData = $this->memberAccount->memberData;
    }

    private function validNpsData(array $overrides = []): array
    {
        return array_merge([
            'branch_id' => $this->branch->id,
            'score' => 8,
            'comment' => null,
            'rating' => 4,
            'review' => null,
        ], $overrides);
    }

    private function createMemberRegistration(string $graduationStatus = 'BELUM_LULUS'): int
    {
        $program = Program::factory()->create();

        return DB::table('members_registration')->insertGetId([
            'members_data_id' => $this->memberData->id,
            'program_id' => $program->id,
            'employee_id' => $this->employeeId,
            'receipt_member_name' => $this->memberData->full_name,
            'receipt_institution_name' => 'Test Institution',
            'receipt_program_name' => $program->program_name,
            'original_price' => 1000000,
            'final_price' => 1000000,
            'graduation_status' => $graduationStatus,
            'payment_status' => 'paid_full',
            'installment_type' => '1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    // NR-01: Submit NPS score berhasil — route sekarang ada (GAP-157 fixed)
    public function test_member_can_submit_nps_score(): void
    {
        $this->actingAs($this->memberAccount, 'member')
            ->post(route('member.nps-responses.store'), $this->validNpsData())
            ->assertRedirect();

        $this->assertDatabaseHas('member_nps_responses', [
            'member_id' => $this->memberData->id,
            'score' => 8,
            'branch_id' => $this->branch->id,
        ]);
    }

    // NR-02: Comment < 10 char ter-reject — min:10 enforced untuk nilai non-null
    public function test_short_comment_is_rejected(): void
    {
        $this->actingAs($this->memberAccount, 'member')
            ->post(route('member.nps-responses.store'), $this->validNpsData([
                'comment' => 'ok', // < 10 chars
            ]))
            ->assertSessionHasErrors('comment');
    }

    // NR-02b: Comment null lolos (nullable)
    public function test_null_comment_passes_validation(): void
    {
        $this->actingAs($this->memberAccount, 'member')
            ->post(route('member.nps-responses.store'), $this->validNpsData([
                'comment' => null,
            ]))
            ->assertRedirect();
    }

    // NR-03: Submit combined form — MemberNpsResponse + MemberReview keduanya dibuat jika ada registrasi LULUS
    public function test_combined_submit_creates_both_nps_and_review_when_graduated(): void
    {
        $this->createMemberRegistration('LULUS');

        $this->actingAs($this->memberAccount, 'member')
            ->post(route('member.nps-responses.store'), $this->validNpsData([
                'rating' => 5,
                'review' => 'Kelas sangat bagus dan bermanfaat',
            ]))
            ->assertRedirect();

        $this->assertDatabaseHas('member_nps_responses', [
            'member_id' => $this->memberData->id,
            'score' => 8,
        ]);

        $this->assertDatabaseHas('member_reviews', [
            'rating' => 5,
            'review' => 'Kelas sangat bagus dan bermanfaat',
        ]);
    }

    // NR-03b: Tidak ada registrasi LULUS — hanya MemberNpsResponse yang dibuat
    public function test_nps_submit_without_graduated_registration_skips_review(): void
    {
        $this->createMemberRegistration('BELUM_LULUS');

        $this->actingAs($this->memberAccount, 'member')
            ->post(route('member.nps-responses.store'), $this->validNpsData([
                'rating' => 4,
                'review' => 'Test review yang cukup panjang',
            ]))
            ->assertRedirect();

        $this->assertDatabaseHas('member_nps_responses', ['member_id' => $this->memberData->id]);
        $this->assertDatabaseMissing('member_reviews', ['rating' => 4]);
    }

    // NR-04: Member edit review milik sendiri — endpoint ada (GAP-161 fixed)
    public function test_member_can_edit_own_review(): void
    {
        $registrationId = $this->createMemberRegistration('LULUS');

        $review = MemberReview::create([
            'member_registration_id' => $registrationId,
            'rating' => 3,
            'review' => 'Review awal yang perlu diubah',
        ]);

        $this->actingAs($this->memberAccount, 'member')
            ->put(route('member.reviews.update', $review), [
                'rating' => 5,
                'review' => 'Review yang sudah diperbaiki',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('member_reviews', [
            'id' => $review->id,
            'rating' => 5,
            'review' => 'Review yang sudah diperbaiki',
        ]);
    }

    // NR-04b: Member tidak bisa edit review milik member lain → 403
    public function test_member_cannot_edit_another_members_review(): void
    {
        $otherAccount = $this->createMember();
        $otherData = $otherAccount->memberData;

        $program = Program::factory()->create();

        $otherRegistrationId = DB::table('members_registration')->insertGetId([
            'members_data_id' => $otherData->id,
            'program_id' => $program->id,
            'employee_id' => $this->employeeId,
            'receipt_member_name' => $otherData->full_name,
            'receipt_institution_name' => 'Other Institution',
            'receipt_program_name' => $program->program_name,
            'original_price' => 500000,
            'final_price' => 500000,
            'graduation_status' => 'LULUS',
            'payment_status' => 'paid_full',
            'installment_type' => '1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $review = MemberReview::create([
            'member_registration_id' => $otherRegistrationId,
            'rating' => 4,
            'review' => 'Review milik member lain',
        ]);

        $this->actingAs($this->memberAccount, 'member')
            ->put(route('member.reviews.update', $review), [
                'rating' => 1,
                'review' => 'Dicoba diubah oleh member berbeda',
            ])
            ->assertStatus(403);
    }

    // NR-05: NPS store() di Admin controller menggunakan auth('member') — intentional design
    // Employee yang login via guard default tidak bisa submit NPS
    public function test_employee_cannot_submit_nps_via_member_route(): void
    {
        $this->actingAs($this->boardUser)
            ->post(route('member.nps-responses.store'), $this->validNpsData())
            ->assertRedirect(route('member.login'));
    }

    // NR-06: Admin list NPS responses — employee dengan permission member.manage bisa akses
    public function test_admin_with_permission_can_list_nps_responses(): void
    {
        $this->actingAs($this->boardUser)
            ->get(route('nps-responses.index'))
            ->assertOk();
    }

    // NR-07: Admin list tanpa permission → 403 (abort_unless sekarang ada, GAP-164 fixed)
    public function test_admin_without_permission_cannot_list_nps_responses(): void
    {
        $regularUser = User::factory()->create();
        $regularUser->assignRole('HRR');

        $this->actingAs($regularUser)
            ->get(route('nps-responses.index'))
            ->assertStatus(403);
    }

    // NR-07b: Employee dengan permission member.manage bisa akses
    public function test_employee_with_member_manage_can_list_nps_responses(): void
    {
        $user = User::factory()->create();
        $user->assignRole('HRR');
        $permission = Permission::firstOrCreate(['name' => 'member.manage', 'guard_name' => 'web']);
        $user->givePermissionTo($permission);
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->actingAs($user)
            ->get(route('nps-responses.index'))
            ->assertOk();
    }

    // NR-08: Admin approve review — sets is_approved=true (GAP-159/160 fixed)
    public function test_admin_can_approve_member_review(): void
    {
        $registrationId = $this->createMemberRegistration('LULUS');

        $review = MemberReview::create([
            'member_registration_id' => $registrationId,
            'rating' => 4,
            'review' => 'Review menunggu approval',
            // is_approved defaults to 0 (false) via DB default
        ]);

        $this->actingAs($this->boardUser)
            ->post(route('member-reviews.approve', $review))
            ->assertRedirect();

        $this->assertDatabaseHas('member_reviews', [
            'id' => $review->id,
            'is_approved' => true,
        ]);
    }

    // NR-09: Admin reject review — sets is_approved=false (GAP-160 fixed)
    public function test_admin_can_reject_member_review(): void
    {
        $registrationId = $this->createMemberRegistration('LULUS');

        $review = MemberReview::create([
            'member_registration_id' => $registrationId,
            'rating' => 2,
            'review' => 'Review yang akan ditolak admin',
            'is_approved' => true,
        ]);

        $this->actingAs($this->boardUser)
            ->post(route('member-reviews.reject', $review))
            ->assertRedirect();

        $this->assertDatabaseHas('member_reviews', [
            'id' => $review->id,
            'is_approved' => false,
        ]);
    }

    // NR-10: MemberReview model punya field is_approved — verifikasi tidak ada bug (GAP-159 fixed)
    public function test_member_review_has_is_approved_field(): void
    {
        $registrationId = $this->createMemberRegistration('LULUS');

        $review = MemberReview::create([
            'member_registration_id' => $registrationId,
            'rating' => 3,
            'review' => 'Review dengan is_approved field',
            'is_approved' => true,
            'approved_at' => now(),
        ]);

        $this->assertNotNull($review->id);
        $this->assertTrue($review->is_approved);
        $this->assertNotNull($review->approved_at);
    }

    // NR-11: Trigger notifikasi saat graduation_status → LULUS (GAP-163)
    // ⚠️ BUG: tidak ada event/observer trigger — test ini mendokumentasikan bug aktif
    public function test_graduation_does_not_trigger_review_notification(): void
    {
        $registrationId = $this->createMemberRegistration('BELUM_LULUS');

        // Update graduation_status ke LULUS — tidak ada observer/event yang trigger notifikasi
        DB::table('members_registration')
            ->where('id', $registrationId)
            ->update(['graduation_status' => 'LULUS', 'updated_at' => now()]);

        // ⚠️ GAP-163: tidak ada notifikasi yang terkirim ke member
        $this->assertDatabaseHas('members_registration', [
            'id' => $registrationId,
            'graduation_status' => 'LULUS',
        ]);

        // Tidak ada MemberNpsResponse ter-trigger (harusnya ada notifikasi/prompt ke member)
        $this->assertDatabaseMissing('member_nps_responses', [
            'member_id' => $this->memberData->id,
        ]);
    }

}
