<?php

namespace Tests\Feature\Organization;

use App\Models\Institution;
use App\Models\MemberData;
use App\Models\Region;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstitutionTest extends TestCase
{
    use RefreshDatabase;

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

    // I-01
    public function test_can_create_institution_with_permission(): void
    {
        $this->actingAs($this->ceoUser)
            ->post('/institutions', [
                'regions_id' => $this->region->id,
                'jenjang_institution' => 'SMA',
                'institution_name' => 'SMA Negeri 1',
            ])
            ->assertRedirect(route('institutions.index'));

        $this->assertDatabaseHas('institutions', ['institution_name' => 'SMA Negeri 1']);
    }

    // I-02
    public function test_can_create_institution_with_same_name_in_different_region(): void
    {
        $otherRegion = Region::factory()->create();
        Institution::factory()->create(['regions_id' => $this->region->id, 'institution_name' => 'SMA Negeri 1']);

        $this->actingAs($this->ceoUser)
            ->post('/institutions', [
                'regions_id' => $otherRegion->id,
                'jenjang_institution' => 'SMA',
                'institution_name' => 'SMA Negeri 1',
            ])
            ->assertRedirect(route('institutions.index'));

        $this->assertDatabaseCount('institutions', 2);
    }

    // I-03
    public function test_cannot_create_institution_with_invalid_jenjang(): void
    {
        $this->actingAs($this->ceoUser)
            ->post('/institutions', [
                'regions_id' => $this->region->id,
                'jenjang_institution' => 'S2',
                'institution_name' => 'Test',
            ])
            ->assertSessionHasErrors('jenjang_institution');
    }

    // I-04
    public function test_cannot_create_institution_with_invalid_region(): void
    {
        $this->actingAs($this->ceoUser)
            ->post('/institutions', [
                'regions_id' => 99999,
                'jenjang_institution' => 'SMA',
                'institution_name' => 'Test',
            ])
            ->assertSessionHasErrors('regions_id');
    }

    // I-05
    public function test_cannot_create_institution_without_permission(): void
    {
        $this->actingAs($this->regularUser)
            ->post('/institutions', [
                'regions_id' => $this->region->id,
                'jenjang_institution' => 'SMA',
                'institution_name' => 'Test',
            ])
            ->assertStatus(403);
    }

    // I-06
    public function test_can_update_institution_with_permission(): void
    {
        $institution = Institution::factory()->create(['regions_id' => $this->region->id]);

        $this->actingAs($this->ceoUser)
            ->patch("/institutions/{$institution->id}", [
                'jenjang_institution' => 'SMP',
                'institution_name' => 'Updated Name',
            ])
            ->assertRedirect(route('institutions.index'));

        $this->assertDatabaseHas('institutions', [
            'id' => $institution->id,
            'institution_name' => 'Updated Name',
            'jenjang_institution' => 'SMP',
        ]);
    }

    // I-07: regions_id immutable — stripped from UpdateInstitutionRequest (GAP-20 fixed)
    public function test_update_institution_ignores_regions_id_change(): void
    {
        $institution = Institution::factory()->create(['regions_id' => $this->region->id]);
        $otherRegion = Region::factory()->create();

        $this->actingAs($this->ceoUser)
            ->patch("/institutions/{$institution->id}", [
                'regions_id' => $otherRegion->id,
                'jenjang_institution' => 'SMA',
                'institution_name' => $institution->institution_name,
            ])
            ->assertRedirect(route('institutions.index'));

        $this->assertDatabaseHas('institutions', [
            'id' => $institution->id,
            'regions_id' => $this->region->id,
        ]);
    }

    // I-08
    public function test_cannot_update_institution_without_permission(): void
    {
        $institution = Institution::factory()->create(['regions_id' => $this->region->id]);

        $this->actingAs($this->regularUser)
            ->patch("/institutions/{$institution->id}", [
                'jenjang_institution' => 'SMP',
                'institution_name' => 'Test',
            ])
            ->assertStatus(403);
    }

    // I-09
    public function test_can_delete_institution_without_members(): void
    {
        $institution = Institution::factory()->create(['regions_id' => $this->region->id]);

        $this->actingAs($this->ceoUser)
            ->delete("/institutions/{$institution->id}")
            ->assertRedirect(route('institutions.index'));

        $this->assertDatabaseMissing('institutions', ['id' => $institution->id]);
    }

    // I-10
    public function test_cannot_delete_institution_with_members(): void
    {
        $institution = Institution::factory()->create(['regions_id' => $this->region->id]);
        MemberData::factory()->create(['institution_id' => $institution->id]);

        $this->actingAs($this->ceoUser)
            ->delete("/institutions/{$institution->id}")
            ->assertRedirect();

        $this->assertDatabaseHas('institutions', ['id' => $institution->id]);
    }

    // I-11
    public function test_cannot_delete_institution_without_permission(): void
    {
        $institution = Institution::factory()->create(['regions_id' => $this->region->id]);

        $this->actingAs($this->regularUser)
            ->delete("/institutions/{$institution->id}")
            ->assertStatus(403);
    }
}
