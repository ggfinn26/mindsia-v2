<?php

use App\Models\ToeflTest;
use App\Models\ToeflSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\get;
use function Pest\Laravel\withSession;

uses(RefreshDatabase::class);

beforeEach(function () {
    \App\Models\Employee::factory()->create(['id' => 1]);
    $this->toeflTest = ToeflTest::create([
        'created_by_employee_id' => 1,
        'test_name' => 'Guest Trial Test',
                'listening_time_limit' => 30,
        'structure_time_limit' => 25,
        'reading_time_limit' => 55,
        'status' => ToeflTest::STATUS_PUBLISHED,
        'is_trial' => true,
    ]);
});

it('cannot view result if session is not completed', function () {
    $session = ToeflSession::create([
        'toefl_test_id' => $this->toeflTest->id,
        'guest_name' => 'Guest User',
        'guest_email' => 'guest@example.com',
        'guest_phone' => '08123456789',
        'status' => ToeflSession::STATUS_IN_PROGRESS,
    ]);

    withSession(['guest_toefl_session_id' => $session->id])
        ->get(route('toefl.guest.result', $session))
        ->assertNotFound();
});

it('can view result if session is completed', function () {
    $session = ToeflSession::create([
        'toefl_test_id' => $this->toeflTest->id,
        'guest_name' => 'Guest User',
        'guest_email' => 'guest@example.com',
        'guest_phone' => '08123456789',
        'status' => ToeflSession::STATUS_COMPLETED,
    ]);

    withSession(['guest_toefl_session_id' => $session->id])
        ->get(route('toefl.guest.result', $session))
        ->assertOk();
});
