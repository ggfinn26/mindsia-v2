<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

it('forbids position attendance settings without permission', function () {
    actingAs(User::factory()->create())
        ->get(route('attendance.position-settings.index'))
        ->assertForbidden();
});

it('allows position attendance settings with permission', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('attendance.schedule_rule.manage');

    actingAs($user)
        ->get(route('attendance.position-settings.index'))
        ->assertOk();
});
