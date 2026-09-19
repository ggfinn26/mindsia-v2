<?php

use App\Models\Employee;
use App\Models\MemberAccount;
use App\Models\MemberData;
use App\Models\ToeflSession;
use App\Models\ToeflTest;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $employee = Employee::factory()->create();
    $this->memberData = MemberData::create([
        'full_name' => 'Member User',
        'whatsapp_number' => '08123456789',
        'status' => 'active',
    ]);
    $this->member = MemberAccount::create([
        'members_data_id' => $this->memberData->id,
        'email' => 'member@example.com',
        'password' => bcrypt('password'),
        'email_verified_at' => now(),
        'is_active' => true,
    ]);
    $this->toeflTest = ToeflTest::create([
        'created_by_employee_id' => $employee->id,
        'test_name' => 'Member Test',
        'listening_time_limit' => 30,
        'structure_time_limit' => 25,
        'reading_time_limit' => 55,
        'status' => ToeflTest::STATUS_PUBLISHED,
    ]);
});

it('can view toefl sessions index', function () {
    actingAs($this->member, 'member')->get(route('toefl.session.index'))->assertOk();
});

it('can start a member session', function () {
    actingAs($this->member, 'member')->post(route('toefl.session.start', $this->toeflTest), [
        'password' => 'some-password', // if test has a password, we test it. If not, should be fine.
    ])->assertRedirect();

    expect(ToeflSession::count())->toBe(1);

    $session = ToeflSession::first();
    expect($session->members_data_id)->toBe($this->memberData->id);
    expect($session->status)->toBe(ToeflSession::STATUS_IN_PROGRESS);
});
