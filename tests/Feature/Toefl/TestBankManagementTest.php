<?php

use App\Models\Employee;
use App\Models\ToeflQuestion;
use App\Models\ToeflTest;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->employee = Employee::factory()->create();
    $this->admin = User::factory()->create(['employee_id' => $this->employee->id]);
    Gate::before(fn ($user, $ability) => true);
});

it('can list toefl tests', function () {
    actingAs($this->admin)->get(route('toefl-tests.index'))->assertOk();
});

it('can create a toefl test', function () {
    actingAs($this->admin)->post(route('toefl-tests.store'), [
        'created_by_employee_id' => $this->employee->id,
        'test_name' => 'Test 1',
        'listening_time_limit' => 30,
        'structure_time_limit' => 25,
        'reading_time_limit' => 55,
    ])->assertRedirect();

    expect(ToeflTest::count())->toBe(1);
});

it('can update a toefl test', function () {
    $test = ToeflTest::create([
        'created_by_employee_id' => $this->employee->id,
        'test_name' => 'Test 1',
        'listening_time_limit' => 30,
        'structure_time_limit' => 25,
        'reading_time_limit' => 55,
        'status' => ToeflTest::STATUS_DRAFT,
    ]);

    actingAs($this->admin)->put(route('toefl-tests.update', $test), [
        'created_by_employee_id' => $this->employee->id,
        'test_name' => 'Test 1 Updated',
        'listening_time_limit' => 35,
        'structure_time_limit' => 30,
        'reading_time_limit' => 60,
    ])->assertRedirect();

    expect($test->fresh()->test_name)->toBe('Test 1 Updated');
});

it('can publish a toefl test', function () {
    $test = ToeflTest::create([
        'created_by_employee_id' => $this->employee->id,
        'test_name' => 'Test 1',
        'listening_time_limit' => 30,
        'structure_time_limit' => 25,
        'reading_time_limit' => 55,
        'status' => ToeflTest::STATUS_DRAFT,
    ]);

    // Create a question so we can publish
    ToeflQuestion::create([
        'toefl_test_id' => $test->id,
        'section' => 'listening',
        'question_text' => 'What is this?',
        'option_a' => 'A',
        'option_b' => 'B',
        'option_c' => 'C',
        'option_d' => 'D',
        'correct_option' => 'A',
        'points' => 1,
        'display_order' => 1,
        'is_active' => true,
    ]);

    actingAs($this->admin)->post(route('toefl-tests.publish', $test))->assertRedirect();

    expect($test->fresh()->status)->toBe(ToeflTest::STATUS_PUBLISHED);
});

it('can delete a toefl test', function () {
    $test = ToeflTest::create([
        'created_by_employee_id' => $this->employee->id,
        'test_name' => 'Test 1',
        'listening_time_limit' => 30,
        'structure_time_limit' => 25,
        'reading_time_limit' => 55,
        'status' => ToeflTest::STATUS_DRAFT,
    ]);

    actingAs($this->admin)->delete(route('toefl-tests.destroy', $test))->assertRedirect();

    expect(ToeflTest::count())->toBe(0);
});

it('can unpublish a published toefl test without sessions', function () {
    $test = ToeflTest::create([
        'created_by_employee_id' => $this->employee->id,
        'test_name' => 'Test 1',
        'listening_time_limit' => 30,
        'structure_time_limit' => 25,
        'reading_time_limit' => 55,
        'status' => ToeflTest::STATUS_PUBLISHED,
        'published_by_employee_id' => $this->employee->id,
        'published_at' => now(),
    ]);

    actingAs($this->admin)->post(route('toefl-tests.unpublish', $test))->assertRedirect();

    expect($test->fresh())->status->toBe(ToeflTest::STATUS_DRAFT)->published_at->toBeNull();
    actingAs($this->admin)->get(route('toefl-tests.edit', $test))->assertOk();
});

it('shows unpublish action instead of edit for published tests on the index', function () {
    $test = ToeflTest::create([
        'created_by_employee_id' => $this->employee->id,
        'test_name' => 'Published Test',
        'listening_time_limit' => 30,
        'structure_time_limit' => 25,
        'reading_time_limit' => 55,
        'status' => ToeflTest::STATUS_PUBLISHED,
    ]);

    actingAs($this->admin)->get(route('toefl-tests.index'))
        ->assertOk()
        ->assertSee('Ya, Kembalikan', false)
        ->assertDontSee(route('toefl-tests.edit', $test), false);
});
