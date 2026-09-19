<?php

use App\Models\Employee;
use App\Models\ToeflSession;
use App\Models\ToeflTest;
use App\Services\Toefl\ToeflScoringService;

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

it('can submit listening section and move to structure', function () {
    // Mock scoring so we don't need scaling data
    $this->mock(ToeflScoringService::class, function ($mock) {
        $mock->shouldReceive('calculateTotal')->withAnyArgs()->andReturn(500);
        $mock->shouldReceive('calculateSectionScore')->withAnyArgs()->andReturn(50);
        $mock->shouldReceive('convertListening')->withAnyArgs()->andReturn(50);
        $mock->shouldReceive('convertStructure')->withAnyArgs()->andReturn(50);
        $mock->shouldReceive('convertReading')->withAnyArgs()->andReturn(50);
    });

    withSession(['guest_toefl_session_id' => $this->session->id])
        ->post(route('toefl.guest.submit-section', $this->session), [
            'section' => 'listening',
        ])
        ->assertRedirect(route('toefl.guest.structure', $this->session));

    expect($this->session->fresh()->listening_submitted_at)->not->toBeNull();
});

it('can submit structure section and move to reading', function () {
    // Mock scoring so we don't need scaling data
    $this->mock(ToeflScoringService::class, function ($mock) {
        $mock->shouldReceive('calculateTotal')->withAnyArgs()->andReturn(500);
        $mock->shouldReceive('calculateSectionScore')->withAnyArgs()->andReturn(50);
        $mock->shouldReceive('convertListening')->withAnyArgs()->andReturn(50);
        $mock->shouldReceive('convertStructure')->withAnyArgs()->andReturn(50);
        $mock->shouldReceive('convertReading')->withAnyArgs()->andReturn(50);
    });

    withSession(['guest_toefl_session_id' => $this->session->id])
        ->post(route('toefl.guest.submit-section', $this->session), [
            'section' => 'structure',
        ])
        ->assertRedirect(route('toefl.guest.reading', $this->session));

    expect($this->session->fresh()->structure_submitted_at)->not->toBeNull();
});

it('can submit reading section and complete session', function () {
    // Mock scoring so we don't need scaling data
    $this->mock(ToeflScoringService::class, function ($mock) {
        $mock->shouldReceive('calculateTotal')->withAnyArgs()->andReturn(500);
        $mock->shouldReceive('calculateSectionScore')->withAnyArgs()->andReturn(50);
        $mock->shouldReceive('convertListening')->withAnyArgs()->andReturn(50);
        $mock->shouldReceive('convertStructure')->withAnyArgs()->andReturn(50);
        $mock->shouldReceive('convertReading')->withAnyArgs()->andReturn(50);
    });

    // Give the session fake previous scores so total calculation works
    $this->session->update(['score_listening' => 50, 'score_structure' => 50]);

    withSession(['guest_toefl_session_id' => $this->session->id])
        ->post(route('toefl.guest.submit-section', $this->session), [
            'section' => 'reading',
        ])
        ->assertRedirect(route('toefl.guest.result', $this->session));

    expect($this->session->fresh()->reading_submitted_at)->not->toBeNull();
    expect($this->session->fresh()->status)->toBe(ToeflSession::STATUS_COMPLETED);
});
