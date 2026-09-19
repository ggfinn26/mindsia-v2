<?php

namespace Tests\Feature\Attendance;

use App\Models\Holiday;
use App\Models\User;
use Tests\TestCase;

class HolidayCrudTest extends TestCase
{
    private User $authorizedUser;

    private User $unauthorizedUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authorizedUser = User::factory()->create();
        $this->authorizedUser->assignRole('Super Admin');

        $this->unauthorizedUser = User::factory()->create();
        $this->unauthorizedUser->assignRole('REGULAR_TUTOR');
    }

    // WS-11 — Create holiday berhasil
    public function test_create_holiday_berhasil(): void
    {
        $this->actingAs($this->authorizedUser)
            ->post(route('holidays.store'), [
                'holiday_name' => 'Hari Raya Nasional',
                'holiday_start_date' => '2026-08-17',
                'holiday_end_date' => '2026-08-17',
            ])
            ->assertRedirect(route('work-schedule-rules.index'));

        $this->assertDatabaseHas('holidays', ['holiday_name' => 'Hari Raya Nasional']);
    }

    // WS-12 — Create holiday end_date sebelum start_date
    public function test_create_holiday_end_date_sebelum_start_date(): void
    {
        $this->actingAs($this->authorizedUser)
            ->post(route('holidays.store'), [
                'holiday_name' => 'Bad Holiday',
                'holiday_start_date' => '2026-08-20',
                'holiday_end_date' => '2026-08-17',
            ])
            ->assertRedirect()
            ->assertSessionHasErrors('holiday_end_date');
    }

    // WS-13 — Create holiday tanpa permission
    public function test_create_holiday_tanpa_permission(): void
    {
        $this->actingAs($this->unauthorizedUser)
            ->post(route('holidays.store'), [
                'holiday_name' => 'Blocked Holiday',
                'holiday_start_date' => '2026-12-25',
                'holiday_end_date' => '2026-12-25',
            ])
            ->assertStatus(403);
    }

    // WS-14 — Update holiday berhasil
    public function test_update_holiday_berhasil(): void
    {
        $holiday = Holiday::factory()->create(['holiday_name' => 'Old Name']);

        $this->actingAs($this->authorizedUser)
            ->patch(route('holidays.update', $holiday), [
                'holiday_name' => 'New Name',
                'holiday_start_date' => $holiday->holiday_start_date->toDateString(),
                'holiday_end_date' => $holiday->holiday_end_date->toDateString(),
            ])
            ->assertRedirect(route('work-schedule-rules.index'));

        $this->assertDatabaseHas('holidays', ['id' => $holiday->id, 'holiday_name' => 'New Name']);
    }

    // WS-15 — Delete holiday
    public function test_delete_holiday_berhasil(): void
    {
        $holiday = Holiday::factory()->create();

        $this->actingAs($this->authorizedUser)
            ->delete(route('holidays.destroy', $holiday))
            ->assertRedirect(route('work-schedule-rules.index'));

        $this->assertDatabaseMissing('holidays', ['id' => $holiday->id]);
    }
}
