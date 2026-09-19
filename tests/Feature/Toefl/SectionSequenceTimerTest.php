<?php

use App\Models\Employee;
use App\Models\ToeflSession;
use App\Models\ToeflTest;

use function Pest\Laravel\withSession;

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

    $this->session = ToeflSession::create([
        'toefl_test_id' => $this->toeflTest->id,
        'guest_name' => 'Guest',
        'status' => ToeflSession::STATUS_IN_PROGRESS,
    ]);
});

it('exposes correct time limit to view for listening', function () {
    $response = withSession(['guest_toefl_session_id' => $this->session->id])
        ->get(route('toefl.guest.listening', $this->session))
        ->assertOk();

    $response->assertViewHas('timeLimit', 30);
});

it('exposes correct time limit to view for structure', function () {
    $response = withSession(['guest_toefl_session_id' => $this->session->id])
        ->get(route('toefl.guest.structure', $this->session))
        ->assertOk();

    $response->assertViewHas('timeLimit', 25);
});

it('exposes correct time limit to view for reading', function () {
    $response = withSession(['guest_toefl_session_id' => $this->session->id])
        ->get(route('toefl.guest.reading', $this->session))
        ->assertOk();

    $response->assertViewHas('timeLimit', 55);
});
