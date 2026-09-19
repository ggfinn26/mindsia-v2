<?php

use App\Models\Employee;
use App\Models\ToeflSession;
use App\Models\ToeflTest;

use function Pest\Laravel\get;
use function Pest\Laravel\post;

beforeEach(function () {
    $employee = Employee::factory()->create();
    $this->toeflTest = ToeflTest::create([
        'created_by_employee_id' => $employee->id,
        'test_name' => 'Guest Trial Test',
        'listening_time_limit' => 30,
        'structure_time_limit' => 25,
        'reading_time_limit' => 55,
        'status' => ToeflTest::STATUS_PUBLISHED,
        'is_trial' => true,
    ]);
});

it('can view entry page for published trial test', function () {
    get(route('toefl.guest.entry', $this->toeflTest))->assertOk();
});

it('cannot view entry page for non-published test', function () {
    $this->toeflTest->update(['status' => ToeflTest::STATUS_DRAFT]);
    get(route('toefl.guest.entry', $this->toeflTest))->assertNotFound();
});

it('cannot view entry page for non-trial test', function () {
    $this->toeflTest->update(['is_trial' => false]);
    get(route('toefl.guest.entry', $this->toeflTest))->assertNotFound();
});

it('can start a guest session', function () {
    post(route('toefl.guest.start', $this->toeflTest), [
        'guest_name' => 'Guest User',
        'guest_email' => 'guest@example.com',
        'guest_whatsapp' => '08123456789',
        'guest_city' => 'Jakarta',
    ])->assertRedirect();

    expect(ToeflSession::count())->toBe(1);

    $session = ToeflSession::first();
    expect($session->guest_name)->toBe('Guest User');
    expect($session->status)->toBe(ToeflSession::STATUS_IN_PROGRESS);

    // Test that the session was put into guest_toefl_session_id
    expect(session('guest_toefl_session_id'))->toBe($session->id);
});
