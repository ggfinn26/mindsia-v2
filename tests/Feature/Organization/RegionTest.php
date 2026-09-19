<?php

namespace Tests\Feature\Organization;

use App\Models\Area;
use App\Models\Institution;
use App\Models\Province;
use App\Models\Region;
use App\Models\User;
use Tests\TestCase;

class RegionTest extends TestCase
{
    private User $ceoUser;

    private User $regularUser;

    private Province $province;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ceoUser = User::factory()->create();
        $this->ceoUser->assignRole('CEO');

        $this->regularUser = User::factory()->create();
        $this->regularUser->assignRole('HRR');

        $this->province = Province::factory()->create();
    }

    // R-01
    public function test_can_create_region_with_permission(): void
    {
        $response = $this->actingAs($this->ceoUser)
            ->post('/regions', [
                'province_id' => $this->province->id,
                'name' => 'Jawa Barat',
            ]);

        $response->assertRedirect(route('regions.index'));
        $this->assertDatabaseHas('regions', ['name' => 'Jawa Barat', 'province_id' => $this->province->id]);
    }

    // R-02
    public function test_cannot_create_region_with_duplicate_name(): void
    {
        Region::factory()->create(['province_id' => $this->province->id, 'name' => 'Jawa Barat']);
        $otherProvince = Province::factory()->create();

        $this->actingAs($this->ceoUser)
            ->post('/regions', ['province_id' => $otherProvince->id, 'name' => 'Jawa Barat'])
            ->assertSessionHasErrors('name');
    }

    // R-03
    public function test_cannot_create_region_with_invalid_province(): void
    {
        $this->actingAs($this->ceoUser)
            ->post('/regions', ['province_id' => 99999, 'name' => 'Test'])
            ->assertSessionHasErrors('province_id');
    }

    // R-04
    public function test_cannot_create_region_without_permission(): void
    {
        $this->actingAs($this->regularUser)
            ->post('/regions', ['province_id' => $this->province->id, 'name' => 'Test'])
            ->assertStatus(403);
    }

    // R-05
    public function test_can_update_region_with_permission(): void
    {
        $region = Region::factory()->create(['province_id' => $this->province->id]);

        $this->actingAs($this->ceoUser)
            ->patch("/regions/{$region->id}", [
                'province_id' => $this->province->id,
                'name' => 'Updated Name',
            ])
            ->assertRedirect(route('regions.index'));

        $this->assertDatabaseHas('regions', ['id' => $region->id, 'name' => 'Updated Name']);
    }

    // R-06
    public function test_can_update_region_with_same_name(): void
    {
        $region = Region::factory()->create(['province_id' => $this->province->id, 'name' => 'Same Name']);

        $this->actingAs($this->ceoUser)
            ->patch("/regions/{$region->id}", [
                'province_id' => $this->province->id,
                'name' => 'Same Name',
            ])
            ->assertRedirect(route('regions.index'));
    }

    // R-07
    public function test_cannot_update_region_without_permission(): void
    {
        $region = Region::factory()->create(['province_id' => $this->province->id]);

        $this->actingAs($this->regularUser)
            ->patch("/regions/{$region->id}", [
                'province_id' => $this->province->id,
                'name' => 'Updated',
            ])
            ->assertStatus(403);
    }

    // R-08
    public function test_can_delete_region_without_children(): void
    {
        $region = Region::factory()->create(['province_id' => $this->province->id]);

        $this->actingAs($this->ceoUser)
            ->delete("/regions/{$region->id}")
            ->assertRedirect(route('regions.index'));

        $this->assertDatabaseMissing('regions', ['id' => $region->id]);
    }

    // R-09
    public function test_cannot_delete_region_with_areas(): void
    {
        $region = Region::factory()->create(['province_id' => $this->province->id]);
        Area::factory()->create(['region_id' => $region->id]);

        $this->actingAs($this->ceoUser)
            ->delete("/regions/{$region->id}")
            ->assertRedirect();

        $this->assertDatabaseHas('regions', ['id' => $region->id]);
    }

    // R-10
    public function test_cannot_delete_region_with_institutions(): void
    {
        $region = Region::factory()->create(['province_id' => $this->province->id]);
        Institution::factory()->create(['regions_id' => $region->id]);

        $this->actingAs($this->ceoUser)
            ->delete("/regions/{$region->id}")
            ->assertRedirect();

        $this->assertDatabaseHas('regions', ['id' => $region->id]);
    }

    // R-11
    public function test_cannot_delete_region_without_permission(): void
    {
        $region = Region::factory()->create(['province_id' => $this->province->id]);

        $this->actingAs($this->regularUser)
            ->delete("/regions/{$region->id}")
            ->assertStatus(403);
    }
}
