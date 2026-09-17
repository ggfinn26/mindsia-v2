<?php

namespace Tests\Feature\Member;

use App\Models\MemberAccount;
use App\Models\MemberData;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesMember;
use Tests\TestCase;

// Flow: member-profile-management (MP-01 to MP-09)
// SKENARIO-TESTING.md: Self-service profile edit, password change, email change
class MemberProfileManagementTest extends TestCase
{
    use CreatesMember;
    use RefreshDatabase;

    private MemberAccount $memberAccount;

    private MemberData $memberData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->memberAccount = $this->createMember(
            accountAttrs: ['password' => 'OldPass123!'],
            dataAttrs: ['full_name' => 'Test Member'],
        );
        $this->memberData = $this->memberAccount->memberData;
    }

    // MP-01: Member akses halaman profil — route sekarang ada (GAP-142 fixed)
    public function test_member_can_view_own_profile(): void
    {
        $this->actingAs($this->memberAccount, 'member')
            ->get(route('member.profile.show'))
            ->assertOk();
    }

    // MP-02: Member edit data profil berhasil — endpoint sekarang ada (GAP-142 fixed)
    public function test_member_can_update_own_profile(): void
    {
        $this->actingAs($this->memberAccount, 'member')
            ->put(route('member.profile.update'), [
                'full_name' => 'Nama Baru',
                'whatsapp_number' => '6281299990000',
                'instagram' => '@namabaru',
                'address' => 'Jl. Merdeka No. 1',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('members_data', [
            'id' => $this->memberData->id,
            'full_name' => 'Nama Baru',
            'whatsapp_number' => '6281299990000',
        ]);
    }

    // MP-03: Member tidak bisa edit data member lain — update selalu via auth('member')->user()->memberData
    public function test_member_update_always_targets_own_data(): void
    {
        $otherAccount = $this->createMember(
            dataAttrs: ['full_name' => 'Other Member'],
        );
        $otherData = $otherAccount->memberData;

        // Submit dengan full_name berbeda — tapi karena controller pakai auth('member')->user()->memberData,
        // hanya memberData milik $this->memberAccount yang bisa ter-update
        $this->actingAs($this->memberAccount, 'member')
            ->put(route('member.profile.update'), [
                'full_name' => 'Dikira Bisa Ubah Orang Lain',
                'whatsapp_number' => '628129999',
            ])
            ->assertRedirect();

        // Data member lain tidak berubah
        $this->assertDatabaseHas('members_data', [
            'id' => $otherData->id,
            'full_name' => 'Other Member',
        ]);
    }

    // MP-04: Upload avatar — endpoint belum ada (GAP-144 belum diperbaiki)
    public function test_avatar_upload_endpoint_not_yet_implemented(): void
    {
        $this->actingAs($this->memberAccount, 'member')
            ->post('/member/profile/avatar', [])
            ->assertStatus(404);
    }

    // MP-05: Ganti password berhasil — old password benar
    public function test_member_can_change_password_with_correct_current_password(): void
    {
        $this->actingAs($this->memberAccount, 'member')
            ->post(route('member.password.change.post'), [
                'current_password' => 'OldPass123!',
                'password' => 'NewPass456!',
                'password_confirmation' => 'NewPass456!',
            ])
            ->assertRedirect();

        // Verify new password works (MemberAccount has 'hashed' cast)
        $this->memberAccount->refresh();
        $this->assertTrue(password_verify('NewPass456!', $this->memberAccount->password));
    }

    // MP-06: Ganti password — old password salah → 422
    public function test_member_cannot_change_password_with_wrong_current_password(): void
    {
        $this->actingAs($this->memberAccount, 'member')
            ->post(route('member.password.change.post'), [
                'current_password' => 'WrongPass!',
                'password' => 'NewPass456!',
                'password_confirmation' => 'NewPass456!',
            ])
            ->assertSessionHasErrors('current_password');
    }

    // MP-07: MemberChangePasswordRequest pakai current_password:member (GAP-143 fixed)
    // Guard mismatch sudah diperbaiki — request eksplisit cek guard 'member'
    public function test_change_password_request_uses_member_guard(): void
    {
        // Employee login via default guard — tidak bisa akses route change-password member
        $employee = User::factory()->create();

        $this->actingAs($employee)
            ->post(route('member.password.change.post'), [
                'current_password' => 'OldPass123!',
                'password' => 'NewPass456!',
                'password_confirmation' => 'NewPass456!',
            ])
            ->assertRedirect(route('member.login'));
    }

    // MP-08: Password baru sama dengan password lama → diblok (different:current_password)
    public function test_new_password_same_as_old_is_rejected(): void
    {
        $this->actingAs($this->memberAccount, 'member')
            ->post(route('member.password.change.post'), [
                'current_password' => 'OldPass123!',
                'password' => 'OldPass123!',
                'password_confirmation' => 'OldPass123!',
            ])
            ->assertSessionHasErrors('password');
    }

    // MP-09: Ganti email — saat ini masuk via profile update biasa, tidak ada verifikasi (GAP-145)
    // Endpoint terpisah /member/profile/email belum ada — email update lewat profile update tanpa verifikasi
    public function test_email_change_without_verification_flow(): void
    {
        $this->actingAs($this->memberAccount, 'member')
            ->put(route('member.profile.update'), [
                'full_name' => $this->memberData->full_name,
                'whatsapp_number' => $this->memberData->whatsapp_number,
                'email' => 'newemail@test.com',
            ])
            ->assertRedirect();

        // ⚠️ GAP-145: email ter-update tanpa flow verifikasi (endpoint terpisah belum ada)
        $this->assertDatabaseHas('members_data', [
            'id' => $this->memberData->id,
            'email' => 'newemail@test.com',
        ]);
    }
}
