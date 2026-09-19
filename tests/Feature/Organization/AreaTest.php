<?php

namespace Tests\Feature\Organization;

use App\Models\Area;
use App\Models\Branch;
use App\Models\Region;
use App\Models\User;
use Tests\TestCase;

class AreaTest extends TestCase
{
    private User $ceoUser;

    private User $regularUser;

    private Region $region;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ceoUser = User::factory()->create();
        $this->ceoUser->assignRole('CEO');

        $this->regularUser = User::factory()->create();
        $this->regularUser->assignRole('HRR');

        $this->region = Region::factory()->create();
    }

    // A-01
    public function test_can_create_area_with_permission(): void
    {
        $this->actingAs($this->ceoUser)
            ->post('/areas', ['region_id' => $this->region->id, 'name' => 'Bandung Raya'])
            ->assertRedirect(route('areas.index'));

        $this->assertDatabaseHas('areas', ['name' => 'Bandung Raya', 'region_id' => $this->region->id]);
    }

    // A-02
    public function test_cannot_create_area_with_duplicate_name(): void
    {
        Area::factory()->create(['region_id' => $this->region->id, 'name' => 'Bandung Raya']);
        $otherRegion = Region::factory()->create();

        $this->actingAs($this->ceoUser)
            ->post('/areas', ['region_id' => $otherRegion->id, 'name' => 'Bandung Raya'])
            ->assertSessionHasErrors('name');
    }

    // A-03
    public function test_cannot_create_area_with_invalid_region(): void
    {
        $this->actingAs($this->ceoUser)
            ->post('/areas', ['region_id' => 99999, 'name' => 'Test'])
            ->assertSessionHasErrors('region_id');
    }

    // A-04
    public function test_cannot_create_area_without_permission(): void
    {
        $this->actingAs($this->regularUser)
            ->post('/areas', ['region_id' => $this->region->id, 'name' => 'Test'])
            ->assertStatus(403);
    }

    // A-05
    public function test_can_update_area_with_permission(): void
    {
        $area = Area::factory()->create(['region_id' => $this->region->id]);

        $this->actingAs($this->ceoUser)
            ->patch("/areas/{$area->id}", ['region_id' => $this->region->id, 'name' => 'Updated'])
            ->assertRedirect(route('areas.index'));

        $this->assertDatabaseHas('areas', ['id' => $area->id, 'name' => 'Updated']);
    }

    // A-06
    public function test_can_update_area_with_same_name(): void
    {
        $area = Area::factory()->create(['region_id' => $this->region->id, 'name' => 'Same']);

        $this->actingAs($this->ceoUser)
            ->patch("/areas/{$area->id}", ['region_id' => $this->region->id, 'name' => 'Same'])
            ->assertRedirect(route('areas.index'));
    }

    // A-07
    public function test_cannot_update_area_without_permission(): void
    {
        $area = Area::factory()->create(['region_id' => $this->region->id]);

        $this->actingAs($this->regularUser)
            ->patch("/areas/{$area->id}", ['region_id' => $this->region->id, 'name' => 'Updated'])
            ->assertStatus(403);
    }

    // A-08
    public function test_can_delete_area_without_branches(): void
    {
        $area = Area::factory()->create(['region_id' => $this->region->id]);

        $this->actingAs($this->ceoUser)
            ->delete("/areas/{$area->id}")
            ->assertRedirect(route('areas.index'));

        $this->assertDatabaseMissing('areas', ['id' => $area->id]);
    }

    // A-09
    public function test_cannot_delete_area_with_active_branch(): void
    {
        $area = Area::factory()->create(['region_id' => $this->region->id]);
        Branch::factory()->create(['areas_id' => $area->id, 'is_active' => true]);

        $this->actingAs($this->ceoUser)
            ->delete("/areas/{$area->id}")
            ->assertRedirect();

        $this->assertDatabaseHas('areas', ['id' => $area->id]);
    }

    // A-10 — GAP-14: area dengan branch non-aktif juga tidak boleh terhapus
    public function test_cannot_delete_area_with_inactive_branch(): void
    {
        $area = Area::factory()->create(['region_id' => $this->region->id]);
        Branch::factory()->create(['areas_id' => $area->id, 'is_active' => false]);

        $this->actingAs($this->ceoUser)
            ->delete("/areas/{$area->id}")
            ->assertRedirect();

        $this->assertDatabaseHas('areas', ['id' => $area->id]);
    }

    // A-11
    public function test_cannot_delete_area_without_permission(): void
    {
        $area = Area::factory()->create(['region_id' => $this->region->id]);

        $this->actingAs($this->regularUser)
            ->delete("/areas/{$area->id}")
            ->assertStatus(403);
    }
}
