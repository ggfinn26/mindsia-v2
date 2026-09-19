<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->admin = User::factory()->create([
        'password' => Hash::make('AdminPassword123!'),
    ]);
    $this->admin->givePermissionTo('auth.user.force_reset_password');
});

test('it can view force reset password form', function () {
    $targetUser = User::factory()->create();

    $this->actingAs($this->admin)
        ->get(route('users.reset-password.show', $targetUser))
        ->assertStatus(200);
});

test('it can force reset user password', function () {
    $targetUser = User::factory()->create([
        'password' => Hash::make('old_password'),
    ]);

    $data = [
        'current_password' => 'AdminPassword123!',
        'password' => 'NewSecurePassword123!',
        'password_confirmation' => 'NewSecurePassword123!',
    ];

    $this->actingAs($this->admin)
        ->post(route('users.reset-password.update', $targetUser), $data)
        ->assertRedirect(route('users.show', $targetUser))
        ->assertSessionHas('success');

    $targetUser->refresh();

    expect(Hash::check('NewSecurePassword123!', $targetUser->password))->toBeTrue();
    expect($targetUser->must_change_password)->toBeTrue();
});
