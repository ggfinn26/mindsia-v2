<?php

namespace Tests\Feature\Attendance;

use App\Models\LeavePaySetting;
use App\Models\Position;
use App\Models\User;
use Tests\TestCase;

// Pengaturan dibayar/tidak per posisi lewat UI, termasuk alpha kerja dan alpha sesi.
class LeavePaySettingUiTest extends TestCase
{
    private User $manager;

    private Position $position;

    protected function setUp(): void
    {
        parent::setUp();

        $this->manager = User::factory()->create();
        $this->manager->givePermissionTo('leave.pay_settings.manage');
        $this->position = Position::create(['position_name' => 'Staff'.uniqid(), 'hierarchy_order' => 5]);
    }

    public function test_halaman_menampilkan_kolom_alpha_dan_alpha_sesi_dengan_status_bawaan(): void
    {
        $response = $this->actingAs($this->manager)->get(route('leave-pay-settings.index'));

        $response->assertOk()->assertSee('Alpha Sesi')->assertSee('Alpha');
        $this->assertFalse(LeavePaySetting::isPaidFor($this->position->id, 'absent'));
        $this->assertFalse(LeavePaySetting::isPaidFor($this->position->id, 'absent_session'));
        $this->assertFalse(LeavePaySetting::isPaidFor($this->position->id, 'permission'));
        $this->assertTrue(LeavePaySetting::isPaidFor($this->position->id, 'sick'));
        $this->assertTrue(LeavePaySetting::isPaidFor($this->position->id, 'leave'));
    }

    public function test_alpha_dan_alpha_sesi_bisa_diubah_per_posisi_lewat_ui(): void
    {
        foreach (['absent', 'absent_session'] as $type) {
            $this->actingAs($this->manager)->post(route('leave-pay-settings.upsert'), [
                'position_id' => $this->position->id, 'leave_type' => $type, 'is_paid' => 1,
            ])->assertSessionHasNoErrors();
        }

        $this->assertTrue(LeavePaySetting::isPaidFor($this->position->id, 'absent'));
        $this->assertTrue(LeavePaySetting::isPaidFor($this->position->id, 'absent_session'));

        $this->actingAs($this->manager)->post(route('leave-pay-settings.upsert'), [
            'position_id' => $this->position->id, 'leave_type' => 'absent', 'is_paid' => 0,
        ]);
        $this->assertFalse(LeavePaySetting::isPaidFor($this->position->id, 'absent'));
        $this->assertSame(1, LeavePaySetting::where('position_id', $this->position->id)->where('leave_type', 'absent')->count());
    }

    public function test_pengaturan_satu_posisi_tidak_mempengaruhi_posisi_lain(): void
    {
        $other = Position::create(['position_name' => 'Lain'.uniqid(), 'hierarchy_order' => 5]);

        $this->actingAs($this->manager)->post(route('leave-pay-settings.upsert'), [
            'position_id' => $this->position->id, 'leave_type' => 'absent', 'is_paid' => 1,
        ]);

        $this->assertTrue(LeavePaySetting::isPaidFor($this->position->id, 'absent'));
        $this->assertFalse(LeavePaySetting::isPaidFor($other->id, 'absent'));
    }

    public function test_jenis_tidak_dikenal_dan_tanpa_izin_ditolak(): void
    {
        $this->actingAs($this->manager)->post(route('leave-pay-settings.upsert'), [
            'position_id' => $this->position->id, 'leave_type' => 'telat', 'is_paid' => 1,
        ])->assertSessionHasErrors('leave_type');

        $this->actingAs(User::factory()->create())->post(route('leave-pay-settings.upsert'), [
            'position_id' => $this->position->id, 'leave_type' => 'absent', 'is_paid' => 1,
        ])->assertForbidden();
    }
}
