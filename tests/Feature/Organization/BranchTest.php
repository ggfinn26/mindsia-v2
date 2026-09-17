<?php

namespace Tests\Feature\Organization;

use App\Models\Area;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BranchTest extends TestCase
{
    use RefreshDatabase;

    private User $ceoUser;

    private User $regularUser;

    private Area $area;

    /** @var array<string, mixed> */
    private array $validPayload;

    protected function setUp(): void
    {
        parent::setUp();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();


        

        $this->ceoUser = User::factory()->create();
        $this->ceoUser->assignRole('CEO');

        $this->regularUser = User::factory()->create();
        $this->regularUser->assignRole('HRR');

        $this->area = Area::factory()->create();

        $this->validPayload = [
            'areas_id' => $this->area->id,
            'branch_name' => 'Cabang Test',
            'code_branches' => 'BR-TEST',
            'address' => 'Jl. Test No. 1',
            'whatsapp' => '6281234567890',
            'latitude' => -6.2,
            'longitude' => 106.8,
            'radius_meters' => 500,
        ];
    }

    // B-01
    public function test_can_create_branch_with_permission(): void
    {
        $this->actingAs($this->ceoUser)
            ->post('/branches', $this->validPayload)
            ->assertRedirect(route('branches.index'));

        $this->assertDatabaseHas('branches', ['code_branches' => 'BR-TEST']);
    }

    // B-02
    public function test_cannot_create_branch_without_permission(): void
    {
        $this->actingAs($this->regularUser)
            ->post('/branches', $this->validPayload)
            ->assertStatus(403);
    }

    // B-03
    public function test_cannot_create_branch_with_duplicate_code(): void
    {
        Branch::factory()->create(['areas_id' => $this->area->id, 'code_branches' => 'BR-TEST']);

        $this->actingAs($this->ceoUser)
            ->post('/branches', $this->validPayload)
            ->assertSessionHasErrors('code_branches');
    }

    // B-04
    public function test_cannot_create_branch_with_invalid_area(): void
    {
        $this->actingAs($this->ceoUser)
            ->post('/branches', array_merge($this->validPayload, ['areas_id' => 99999]))
            ->assertSessionHasErrors('areas_id');
    }

    // B-05
    public function test_cannot_create_branch_with_latitude_out_of_range(): void
    {
        $this->actingAs($this->ceoUser)
            ->post('/branches', array_merge($this->validPayload, ['latitude' => 91]))
            ->assertSessionHasErrors('latitude');
    }

    // B-06
    public function test_cannot_create_branch_with_longitude_out_of_range(): void
    {
        $this->actingAs($this->ceoUser)
            ->post('/branches', array_merge($this->validPayload, ['longitude' => 181]))
            ->assertSessionHasErrors('longitude');
    }

    // B-07
    public function test_cannot_create_branch_with_zero_radius(): void
    {
        $this->actingAs($this->ceoUser)
            ->post('/branches', array_merge($this->validPayload, ['radius_meters' => 0]))
            ->assertSessionHasErrors('radius_meters');
    }

    // B-08
    public function test_can_create_branch_without_pic(): void
    {
        $payload = $this->validPayload;
        unset($payload['ma_pic_employee_id']);

        $this->actingAs($this->ceoUser)
            ->post('/branches', $payload)
            ->assertRedirect(route('branches.index'));

        $this->assertDatabaseHas('branches', ['code_branches' => 'BR-TEST', 'ma_pic_employee_id' => null]);
    }

    // B-09
    public function test_can_update_branch_with_permission(): void
    {
        $branch = Branch::factory()->create(['areas_id' => $this->area->id]);

        $this->actingAs($this->ceoUser)
            ->patch("/branches/{$branch->id}", array_merge($this->validPayload, ['branch_name' => 'Updated']))
            ->assertRedirect(route('branches.index'));

        $this->assertDatabaseHas('branches', ['id' => $branch->id, 'branch_name' => 'Updated']);
    }

    // B-10
    public function test_can_update_branch_with_same_code(): void
    {
        $branch = Branch::factory()->create(['areas_id' => $this->area->id, 'code_branches' => 'BR-OWN']);

        $this->actingAs($this->ceoUser)
            ->patch("/branches/{$branch->id}", array_merge($this->validPayload, ['code_branches' => 'BR-OWN']))
            ->assertRedirect(route('branches.index'));
    }

    // B-11
    public function test_cannot_update_branch_with_duplicate_code_from_another(): void
    {
        Branch::factory()->create(['areas_id' => $this->area->id, 'code_branches' => 'BR-OTHER']);
        $branch = Branch::factory()->create(['areas_id' => $this->area->id]);

        $this->actingAs($this->ceoUser)
            ->patch("/branches/{$branch->id}", array_merge($this->validPayload, ['code_branches' => 'BR-OTHER']))
            ->assertSessionHasErrors('code_branches');
    }

    // B-14
    public function test_can_toggle_active_with_permission(): void
    {
        $branch = Branch::factory()->create(['areas_id' => $this->area->id, 'is_active' => true]);

        $this->actingAs($this->ceoUser)
            ->patch("/branches/{$branch->id}/toggle-active")
            ->assertRedirect(route('branches.index'));

        $this->assertDatabaseHas('branches', ['id' => $branch->id, 'is_active' => false]);
    }

    // B-15
    public function test_cannot_toggle_active_without_permission(): void
    {
        $branch = Branch::factory()->create(['areas_id' => $this->area->id]);

        $this->actingAs($this->regularUser)
            ->patch("/branches/{$branch->id}/toggle-active")
            ->assertStatus(403);
    }

    // B-16
    public function test_can_delete_branch_without_employees(): void
    {
        $branch = Branch::factory()->create(['areas_id' => $this->area->id]);

        $this->actingAs($this->ceoUser)
            ->delete("/branches/{$branch->id}")
            ->assertRedirect(route('branches.index'));

        $this->assertDatabaseMissing('branches', ['id' => $branch->id]);
    }

    // B-17
    public function test_cannot_delete_branch_with_employees(): void
    {
        $branch = Branch::factory()->create(['areas_id' => $this->area->id]);
        Employee::factory()->create(['branch_id' => $branch->id]);

        $this->actingAs($this->ceoUser)
            ->delete("/branches/{$branch->id}")
            ->assertRedirect();

        $this->assertDatabaseHas('branches', ['id' => $branch->id]);
    }

    // B-18
    public function test_cannot_delete_branch_without_permission(): void
    {
        $branch = Branch::factory()->create(['areas_id' => $this->area->id]);

        $this->actingAs($this->regularUser)
            ->delete("/branches/{$branch->id}")
            ->assertStatus(403);
    }
}
