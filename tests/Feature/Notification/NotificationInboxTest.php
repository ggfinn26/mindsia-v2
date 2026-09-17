<?php

use App\Models\Employee;
use App\Models\EmployeeNotification;
use App\Models\MemberAccount;
use App\Models\MemberData;
use App\Models\MemberNotification;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(DatabaseTransactions::class);

it('shows employee notifications and marks as read', function () {
    $employee = Employee::factory()->create();
    $user = User::factory()->create(['employee_id' => $employee->id]);

    $notification = EmployeeNotification::factory()->create([
        'employee_id' => $employee->id,
        'status' => 'unread',
    ]);

    $this->actingAs($user)
        ->get(route('notifications.index'))
        ->assertStatus(200);

    $this->assertDatabaseHas('employee_notifications', [
        'id' => $notification->id,
        'status' => 'read',
    ]);
});

it('returns employee unread count', function () {
    $employee = Employee::factory()->create();
    $user = User::factory()->create(['employee_id' => $employee->id]);

    EmployeeNotification::factory()->count(3)->create([
        'employee_id' => $employee->id,
        'status' => 'unread',
    ]);

    $this->actingAs($user)
        ->get(route('notifications.unread-count'))
        ->assertStatus(200)
        ->assertJson(['count' => 3]);
});

it('shows member notifications and marks as read', function () {
    $member = MemberData::factory()->create();
    $memberUser = MemberAccount::factory()->create(['members_data_id' => $member->id]);

    $notification = MemberNotification::factory()->create([
        'member_id' => $member->id,
        'status' => 'unread',
    ]);

    $response = $this->actingAs($memberUser, 'member')
        ->get(route('member.notifications.index'));

    $response->assertStatus(200);

    $this->assertDatabaseHas('member_notifications', [
        'id' => $notification->id,
        'status' => 'read',
    ]);
});

it('returns member unread count', function () {
    $member = MemberData::factory()->create();
    $memberUser = MemberAccount::factory()->create(['members_data_id' => $member->id]);

    MemberNotification::factory()->count(2)->create([
        'member_id' => $member->id,
        'status' => 'unread',
    ]);

    $this->actingAs($memberUser, 'member')
        ->get(route('member.notifications.unread-count'))
        ->assertStatus(200)
        ->assertJson(['count' => 2]);
});
