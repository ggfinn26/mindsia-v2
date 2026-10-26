<?php

use App\Models\MemberPayment;
use App\Models\MemberRegistration;
use Illuminate\Support\Facades\Http;
use Tests\Support\CreatesMember;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class, CreatesMember::class);

beforeEach(function () {
    config()->set('services.doku.client_id', 'CLIENT-X');
    config()->set('services.doku.secret_key', 'secret-key');
    config()->set('services.doku.base_url', 'https://api-sandbox.doku.com');
    config()->set('services.doku.notification_url', 'https://staging.test/webhooks/doku');

    Http::fake(['api-sandbox.doku.com/checkout/v1/payment' => Http::response([
        'response' => ['payment' => ['url' => 'https://sandbox.doku.com/checkout-link-v2/abc', 'token_id' => 't', 'expired_date' => '20261025120000']],
    ], 200)]);
});

function paymentOwnedBy(int $membersDataId, string $status = 'unpaid'): MemberPayment
{
    $registration = MemberRegistration::factory()->create(['members_data_id' => $membersDataId]);

    return MemberPayment::factory()->create([
        'member_registration_id' => $registration->id,
        'payment_status' => $status,
        'amount' => 1500000,
    ]);
}

it('member membuat checkout untuk cicilannya dan redirect ke url DOKU', function () {
    $member = $this->createMember(['email_verified_at' => now(), 'is_active' => true]);
    $payment = paymentOwnedBy($member->members_data_id);

    $this->actingAs($member, 'member')
        ->post(route('member.doku.pay', $payment))
        ->assertRedirect('https://sandbox.doku.com/checkout-link-v2/abc');
});

it('member tidak bisa membayar cicilan milik orang lain (403)', function () {
    $member = $this->createMember(['email_verified_at' => now(), 'is_active' => true]);
    $other = $this->createMember(['email_verified_at' => now(), 'is_active' => true]);
    $foreignPayment = paymentOwnedBy($other->members_data_id);

    $this->actingAs($member, 'member')
        ->post(route('member.doku.pay', $foreignPayment))
        ->assertForbidden();

    Http::assertNothingSent();
});

it('cicilan yang sudah paid ditolak 422', function () {
    $member = $this->createMember(['email_verified_at' => now(), 'is_active' => true]);
    $payment = paymentOwnedBy($member->members_data_id, 'paid');

    $this->actingAs($member, 'member')
        ->post(route('member.doku.pay', $payment))
        ->assertStatus(422);
});

it('guest diarahkan ke login', function () {
    $payment = paymentOwnedBy($this->createMember()->members_data_id);

    $this->post(route('member.doku.pay', $payment))
        ->assertRedirect(route('member.login'));
});
