<?php

use App\Models\Employee;
use App\Models\ToeflAnswer;
use App\Models\ToeflQuestion;
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

    $this->question = ToeflQuestion::create([
        'toefl_test_id' => $this->toeflTest->id,
        'section' => 'listening',
        'question_text' => 'Test Q',
        'option_a' => 'A',
        'option_b' => 'B',
        'option_c' => 'C',
        'option_d' => 'D',
        'correct_option' => 'A',
        'points' => 1,
        'display_order' => 1,
    ]);

    $this->session = ToeflSession::create([
        'toefl_test_id' => $this->toeflTest->id,
        'guest_name' => 'Guest',
        'status' => ToeflSession::STATUS_IN_PROGRESS,
    ]);

    // Seed empty answer row so saveAnswer can update it
    ToeflAnswer::create([
        'toefl_session_id' => $this->session->id,
        'toefl_question_id' => $this->question->id,
        'selected_option' => null,
        'is_correct' => null,
        'answered_at' => null,
    ]);
});

it('can auto-save an answer', function () {
    withSession(['guest_toefl_session_id' => $this->session->id])
        ->postJson(route('toefl.guest.answer', $this->session), [
            'question_id' => $this->question->id,
            'selected_option' => 'A',
        ])
        ->assertOk();

    expect(ToeflAnswer::count())->toBe(1);

    $answer = ToeflAnswer::first();
    expect($answer->toefl_session_id)->toBe($this->session->id);
    expect($answer->toefl_question_id)->toBe($this->question->id);
    expect($answer->selected_option)->toBe('A');
});
