<?php

namespace Tests\Feature\Organization;

use App\Models\Province;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProvinceTest extends TestCase
{
    use RefreshDatabase;

    private User $boardUser;

    private User $nonBoardUser;

    protected function setUp(): void
    {
        parent::setUp();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();


        // Run seeders to create roles and permissions
        

        // Create test users
        $this->boardUser = User::factory()->create();
        $this->boardUser->assignRole('CEO');

        $this->nonBoardUser = User::factory()->create();
        $this->nonBoardUser->assignRole('HRR');
    }

    public function test_board_can_create_province()
    {
        $response = $this->actingAs($this->boardUser)
            ->post('/provinces', ['name' => 'Jawa Barat']);

        $response->assertRedirect('/provinces');
        $this->assertDatabaseHas('provinces', ['name' => 'Jawa Barat']);
    }

    public function test_board_can_update_province()
    {
        $province = Province::factory()->create(['name' => 'Jawa Tengah']);

        $response = $this->actingAs($this->boardUser)
            ->patch("/provinces/{$province->id}", ['name' => 'Jawa Tengah Updated']);

        $response->assertRedirect('/provinces');
        $this->assertDatabaseHas('provinces', ['name' => 'Jawa Tengah Updated']);
    }

    public function test_board_can_delete_province_without_regions()
    {
        $province = Province::factory()->create();

        $response = $this->actingAs($this->boardUser)
            ->delete("/provinces/{$province->id}");

        $response->assertRedirect('/provinces');
        $this->assertDatabaseMissing('provinces', ['id' => $province->id]);
    }

    public function test_non_board_cannot_create_province()
    {
        $response = $this->actingAs($this->nonBoardUser)
            ->post('/provinces', ['name' => 'Jawa Timur']);

        $response->assertStatus(403);
    }

    public function test_non_board_cannot_update_province()
    {
        $province = Province::factory()->create();

        $response = $this->actingAs($this->nonBoardUser)
            ->patch("/provinces/{$province->id}", ['name' => 'Updated']);

        $response->assertStatus(403);
    }

    public function test_non_board_cannot_delete_province()
    {
        $province = Province::factory()->create();

        $response = $this->actingAs($this->nonBoardUser)
            ->delete("/provinces/{$province->id}");

        $response->assertStatus(403);
    }

    public function test_cannot_create_duplicate_province()
    {
        Province::factory()->create(['name' => 'Bali']);

        $this->actingAs($this->boardUser)
            ->post('/provinces', ['name' => 'Bali'])
            ->assertRedirect()
            ->assertSessionHasErrors('name');
    }

    public function test_cannot_create_province_without_name()
    {
        $this->actingAs($this->boardUser)
            ->post('/provinces', [])
            ->assertRedirect()
            ->assertSessionHasErrors('name');
    }

    public function test_cannot_delete_province_with_regions()
    {
        $province = Province::factory()->create();
        $province->regions()->create(['name' => 'Jakarta']);

        $this->actingAs($this->boardUser)
            ->delete("/provinces/{$province->id}")
            ->assertRedirect();

        $this->assertDatabaseHas('provinces', ['id' => $province->id]);
    }

    public function test_unauthenticated_user_redirected_to_login()
    {
        $this->post('/provinces', ['name' => 'Test'])
            ->assertRedirect(route('login'));
    }
}
