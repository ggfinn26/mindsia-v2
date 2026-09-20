<?php

use App\Enums\Member\ActivationStatus;
use App\Models\MemberAccount;
use Tests\Support\CreatesMember;

uses(CreatesMember::class);

function memberWithStatus(ActivationStatus $status): MemberAccount
{
    return test()->createMember(
        ['email_verified_at' => now(), 'is_active' => true],
        ['activation_status' => $status],
    );
}

$protectedRoutes = ['/member/class', '/member/curriculum', '/member/attendance'];

// MA-01: member aktif bisa akses modul
test('MA-01 active member can access class curriculum attendance', function () use ($protectedRoutes) {
    $member = memberWithStatus(ActivationStatus::Aktif);

    foreach ($protectedRoutes as $route) {
        $this->actingAs($member, 'member')->get($route)->assertOk();
    }
});

// MA-02: member pending → show activation view
test('MA-02 pending member sees pending activation view on academic routes', function () use ($protectedRoutes) {
    $member = memberWithStatus(ActivationStatus::Pending);

    foreach ($protectedRoutes as $route) {
        $this->actingAs($member, 'member')
            ->get($route)
            ->assertOk()
            ->assertViewIs('member.activation.status')
            ->assertViewHas('status', 'pending');
    }
});

// MA-03: member ditangguhkan → show activation view
test('MA-03 suspended member sees suspended view on academic routes', function () use ($protectedRoutes) {
    $member = memberWithStatus(ActivationStatus::Ditangguhkan);

    foreach ($protectedRoutes as $route) {
        $this->actingAs($member, 'member')
            ->get($route)
            ->assertOk()
            ->assertViewIs('member.activation.status')
            ->assertViewHas('status', 'ditangguhkan');
    }
});

// MA-04: setelah diaktifkan kembali langsung bisa akses
test('MA-04 reactivated member can access modules again', function () use ($protectedRoutes) {
    $member = memberWithStatus(ActivationStatus::Ditangguhkan);
    $member->memberData->update(['activation_status' => ActivationStatus::Aktif]);

    foreach ($protectedRoutes as $route) {
        $this->actingAs($member, 'member')->get($route)->assertOk();
    }
});

// MA-05: dashboard tetap bisa diakses semua status
test('MA-05 dashboard accessible regardless of activation status', function () {
    foreach (ActivationStatus::cases() as $status) {
        $member = memberWithStatus($status);
        $this->actingAs($member, 'member')
            ->get('/member/dashboard')
            ->assertOk();
    }
});

// MA-06: API requests from inactive members should return 403 JSON
test('MA-06 inactive member gets 403 on json requests', function () use ($protectedRoutes) {
    $member = memberWithStatus(ActivationStatus::Pending);

    foreach ($protectedRoutes as $route) {
        $this->actingAs($member, 'member')
            ->getJson($route)
            ->assertStatus(403)
            ->assertJsonStructure(['message']);
    }
});
