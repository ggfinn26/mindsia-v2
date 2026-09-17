<?php

namespace Tests\Feature\Attendance;

use App\Models\User;
use App\Models\WorkScheduleRule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkScheduleRuleCrudTest extends TestCase
{
    use RefreshDatabase;

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

    // WS-01 — Create rule berhasil
    public function test_create_rule_berhasil(): void
    {
        $this->actingAs($this->authorizedUser)
            ->post(route('work-schedule-rules.store'), [
                'setting_name' => 'Shift Pagi',
                'start_time' => '08:00',
                'end_time' => '17:00',
                'break_start_time' => '12:00',
                'break_end_time' => '13:00',
                'late_tolerance_minutes' => 15,
                'early_leave_tolerance_minutes' => 15,
                'is_required' => true,
                'is_active' => true,
            ])
            ->assertRedirect(route('work-schedule-rules.index'));

        $this->assertDatabaseHas('work_schedule_rules', ['setting_name' => 'Shift Pagi']);
    }

    // WS-02 — Create rule setting_name duplikat
    public function test_create_rule_setting_name_duplikat(): void
    {
        WorkScheduleRule::factory()->create(['setting_name' => 'Existing Shift']);

        $this->actingAs($this->authorizedUser)
            ->post(route('work-schedule-rules.store'), [
                'setting_name' => 'Existing Shift',
                'start_time' => '08:00',
                'end_time' => '17:00',
                'late_tolerance_minutes' => 15,
                'early_leave_tolerance_minutes' => 15,
            ])
            ->assertRedirect()
            ->assertSessionHasErrors('setting_name');
    }

    // WS-03 — Create rule end_time sebelum start_time
    public function test_create_rule_end_time_sebelum_start_time(): void
    {
        $this->actingAs($this->authorizedUser)
            ->post(route('work-schedule-rules.store'), [
                'setting_name' => 'Bad Time Shift',
                'start_time' => '17:00',
                'end_time' => '08:00',
                'late_tolerance_minutes' => 15,
                'early_leave_tolerance_minutes' => 15,
            ])
            ->assertRedirect()
            ->assertSessionHasErrors('end_time');
    }

    // WS-04 — Create rule break_end_time sebelum break_start_time
    public function test_create_rule_break_end_time_sebelum_break_start_time(): void
    {
        $this->actingAs($this->authorizedUser)
            ->post(route('work-schedule-rules.store'), [
                'setting_name' => 'Bad Break Shift',
                'start_time' => '08:00',
                'end_time' => '17:00',
                'break_start_time' => '13:00',
                'break_end_time' => '12:00',
                'late_tolerance_minutes' => 15,
                'early_leave_tolerance_minutes' => 15,
            ])
            ->assertRedirect()
            ->assertSessionHasErrors('break_end_time');
    }

    // WS-05 — Create rule tanpa permission
    public function test_create_rule_tanpa_permission(): void
    {
        $this->actingAs($this->unauthorizedUser)
            ->post(route('work-schedule-rules.store'), [
                'setting_name' => 'Blocked Shift',
                'start_time' => '08:00',
                'end_time' => '17:00',
                'late_tolerance_minutes' => 15,
                'early_leave_tolerance_minutes' => 15,
            ])
            ->assertStatus(403);
    }

    // WS-06 — Update rule berhasil (setting_name sama self)
    public function test_update_rule_berhasil(): void
    {
        $rule = WorkScheduleRule::factory()->create(['setting_name' => 'My Shift']);

        $this->actingAs($this->authorizedUser)
            ->patch(route('work-schedule-rules.update', $rule), [
                'setting_name' => 'My Shift',
                'start_time' => '09:00',
                'end_time' => '18:00',
                'late_tolerance_minutes' => 10,
                'early_leave_tolerance_minutes' => 10,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('work_schedule_rules', [
            'id' => $rule->id,
            'start_time' => '09:00',
        ]);
    }

    // WS-07 — Update rule setting_name duplikat record lain
    public function test_update_rule_setting_name_duplikat_record_lain(): void
    {
        WorkScheduleRule::factory()->create(['setting_name' => 'Taken Name']);
        $rule = WorkScheduleRule::factory()->create(['setting_name' => 'My Shift']);

        $this->actingAs($this->authorizedUser)
            ->patch(route('work-schedule-rules.update', $rule), [
                'setting_name' => 'Taken Name',
                'start_time' => '08:00',
                'end_time' => '17:00',
                'late_tolerance_minutes' => 15,
                'early_leave_tolerance_minutes' => 15,
            ])
            ->assertRedirect()
            ->assertSessionHasErrors('setting_name');
    }

    // WS-08 — Delete rule berhasil (tanpa assignments)
    public function test_delete_rule_berhasil(): void
    {
        $rule = WorkScheduleRule::factory()->create();

        $this->actingAs($this->authorizedUser)
            ->delete(route('work-schedule-rules.destroy', $rule))
            ->assertRedirect(route('work-schedule-rules.index'));

        $this->assertDatabaseMissing('work_schedule_rules', ['id' => $rule->id]);
    }

    // WS-09 — Delete rule masih punya assignments
    public function test_delete_rule_masih_punya_assignments(): void
    {
        $rule = WorkScheduleRule::factory()->create();

        // Buat assignment langsung via DB
        $rule->assignments()->create([
            'assignable_type' => 'role',
            'assignable_id' => 1,
            'effective_start_date' => now()->toDateString(),
            'is_active' => true,
        ]);

        $this->actingAs($this->authorizedUser)
            ->delete(route('work-schedule-rules.destroy', $rule))
            ->assertRedirect();

        // Controller mengembalikan back() dengan error — rule tidak boleh terhapus
        $this->assertDatabaseHas('work_schedule_rules', ['id' => $rule->id]);
    }

    // WS-10 — Delete rule tanpa permission
    public function test_delete_rule_tanpa_permission(): void
    {
        $rule = WorkScheduleRule::factory()->create();

        $this->actingAs($this->unauthorizedUser)
            ->delete(route('work-schedule-rules.destroy', $rule))
            ->assertStatus(403);
    }
}
