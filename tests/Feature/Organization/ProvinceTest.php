<?php

use App\Models\Province;
use App\Models\User;

beforeEach(function () {
    $this->boardUser = User::factory()->create();
    $this->boardUser->assignRole('CEO');

    $this->nonBoardUser = User::factory()->create();
    $this->nonBoardUser->assignRole('HRR');
});

it('board can create province', function () {
    $this->actingAs($this->boardUser)
        ->post('/provinces', ['name' => 'Jawa Barat'])
        ->assertRedirect('/provinces');

    $this->assertDatabaseHas('provinces', ['name' => 'Jawa Barat']);
});

it('board can update province', function () {
    $province = Province::factory()->create(['name' => 'Jawa Tengah']);

    $this->actingAs($this->boardUser)
        ->patch("/provinces/{$province->id}", ['name' => 'Jawa Tengah Updated'])
        ->assertRedirect('/provinces');

    $this->assertDatabaseHas('provinces', ['name' => 'Jawa Tengah Updated']);
});

it('board can delete province without regions', function () {
    $province = Province::factory()->create();

    $this->actingAs($this->boardUser)
        ->delete("/provinces/{$province->id}")
        ->assertRedirect('/provinces');

    $this->assertDatabaseMissing('provinces', ['id' => $province->id]);
});

it('non-board cannot create province', function () {
    $this->actingAs($this->nonBoardUser)
        ->post('/provinces', ['name' => 'Jawa Timur'])
        ->assertStatus(403);
});

it('non-board cannot update province', function () {
    $province = Province::factory()->create();

    $this->actingAs($this->nonBoardUser)
        ->patch("/provinces/{$province->id}", ['name' => 'Updated'])
        ->assertStatus(403);
});

it('non-board cannot delete province', function () {
    $province = Province::factory()->create();

    $this->actingAs($this->nonBoardUser)
        ->delete("/provinces/{$province->id}")
        ->assertStatus(403);
});

it('cannot create duplicate province', function () {
    Province::factory()->create(['name' => 'Bali']);

    $this->actingAs($this->boardUser)
        ->post('/provinces', ['name' => 'Bali'])
        ->assertRedirect()
        ->assertSessionHasErrors('name');
});

it('cannot create province without name', function () {
    $this->actingAs($this->boardUser)
        ->post('/provinces', [])
        ->assertRedirect()
        ->assertSessionHasErrors('name');
});

it('cannot delete province with regions', function () {
    $province = Province::factory()->create();
    $province->regions()->create(['name' => 'Jakarta']);

    $this->actingAs($this->boardUser)
        ->delete("/provinces/{$province->id}")
        ->assertRedirect();

    $this->assertDatabaseHas('provinces', ['id' => $province->id]);
});

it('unauthenticated user redirected to login', function () {
    $this->post('/provinces', ['name' => 'Test'])
        ->assertRedirect(route('login'));
});
