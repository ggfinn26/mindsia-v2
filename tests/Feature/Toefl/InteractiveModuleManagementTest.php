<?php

use App\Models\ToeflTest;
use App\Models\ToeflPassage;
use App\Models\ToeflQuestion;
use App\Models\ToeflMedia;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;
use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->employee = Employee::factory()->create();
    $this->admin = User::factory()->create(['employee_id' => $this->employee->id]);
    Gate::before(fn ($user, $ability) => true);
    
    $this->toeflTest = ToeflTest::create([
        'created_by_employee_id' => $this->employee->id,
        'test_name' => 'Test 1',
        'listening_time_limit' => 30,
        'structure_time_limit' => 25,
        'reading_time_limit' => 55,
        'status' => ToeflTest::STATUS_DRAFT,
    ]);
});

it('can store a passage', function () {
    actingAs($this->admin)->post(route('toefl.passages.store', $this->toeflTest), [
        'section' => 'reading',
        'title' => 'Passage 1',
        'body_text' => 'This is the passage body.',
        'display_order' => 1,
    ])->assertRedirect();

    expect(ToeflPassage::count())->toBe(1);
});

it('can update a passage', function () {
    $passage = ToeflPassage::create([
        'toefl_test_id' => $this->toeflTest->id,
        'section' => 'reading',
        'title' => 'Passage 1',
        'body_text' => 'Body',
        'display_order' => 1,
    ]);

    actingAs($this->admin)->put(route('toefl.passages.update', $passage), [
        'title' => 'Passage Updated',
        'body_text' => 'Body Updated',
        'display_order' => 2,
    ])->assertRedirect();

    expect($passage->fresh()->title)->toBe('Passage Updated');
});

it('can delete a passage', function () {
    $passage = ToeflPassage::create([
        'toefl_test_id' => $this->toeflTest->id,
        'section' => 'reading',
        'title' => 'Passage 1',
        'body_text' => 'Body',
        'display_order' => 1,
    ]);

    actingAs($this->admin)->delete(route('toefl.passages.destroy', $passage))->assertRedirect();

    expect(ToeflPassage::count())->toBe(0);
});

it('can store a question', function () {
    actingAs($this->admin)->post(route('toefl.questions.store', $this->toeflTest), [
        'section' => 'reading',
        'question_text' => 'What is this?',
        'option_a' => 'A',
        'option_b' => 'B',
        'option_c' => 'C',
        'option_d' => 'D',
        'correct_option' => 'A',
        'points' => 1,
        'display_order' => 1,
        'is_active' => true,
    ])->assertRedirect();

    expect(ToeflQuestion::count())->toBe(1);
});

it('can store media', function () {
    Storage::fake('public');
    
    $file = UploadedFile::fake()->create('audio.mp3', 100);
    
    actingAs($this->admin)->post(route('toefl.media.store'), [
        'file' => $file,
        'type' => 'audio',
    ])->assertRedirect();

    expect(ToeflMedia::count())->toBe(1);
});
