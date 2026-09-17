<?php

use App\Models\User;

beforeEach(function () {
    // boardUser: CEO — all permissions available via role
    $this->boardUser = User::factory()->create();
    $this->boardUser->assignRole('CEO');

    // regularUser: HRR — restricted, no marketing payment statement permission
    $this->regularUser = User::factory()->create();
    $this->regularUser->assignRole('HRR');
});

// PS-01: unauthenticated request redirects to login
test('PS-01 unauthenticated access redirects to login', function () {
    $this->get(route('marketing.member-payment-statement'))
        ->assertRedirect(route('login'));
});

// PS-02: authenticated but missing permission returns 403
test('PS-02 missing permission is blocked with 403', function () {
    $this->actingAs($this->regularUser)
        ->get(route('marketing.member-payment-statement'))
        ->assertForbidden();
});

// PS-03: user with permission sees the page (empty data is fine)
test('PS-03 user with permission gets 200', function () {
    $this->boardUser->givePermissionTo('marketing.member_payment_statement.view');

    $this->actingAs($this->boardUser)
        ->get(route('marketing.member-payment-statement'))
        ->assertOk();
});

// PS-04: permission + branch_id filter still returns 200 (empty results for non-existent branch)
test('PS-04 user with permission and branch_id filter gets 200', function () {
    $this->boardUser->givePermissionTo('marketing.member_payment_statement.view');

    $this->actingAs($this->boardUser)
        ->get(route('marketing.member-payment-statement', ['branch_id' => 999]))
        ->assertOk();
});
