<?php

namespace Tests\Feature\Facility;

use App\Models\InventoryItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

// Flow: inventory-management (IM-01+)
// SKENARIO-TESTING.md: Inventory CRUD, adjust quantity, dispose, history
class InventoryManagementTest extends TestCase
{
    private User $boardUser;

    private User $regularUser;

    private int $branchId;

    private int $employeeId;

    protected function setUp(): void
    {
        parent::setUp();

        $uid = uniqid();
        $provinceId = DB::table('provinces')->insertGetId(['name' => "Prov-{$uid}", 'created_at' => now(), 'updated_at' => now()]);
        $regionId = DB::table('regions')->insertGetId(['province_id' => $provinceId, 'name' => "Reg-{$uid}", 'created_at' => now(), 'updated_at' => now()]);
        $areaId = DB::table('areas')->insertGetId(['region_id' => $regionId, 'name' => "Area-{$uid}", 'created_at' => now(), 'updated_at' => now()]);
        $this->branchId = DB::table('branches')->insertGetId([
            'areas_id' => $areaId, 'branch_name' => "Branch-{$uid}", 'code_branches' => "BR-{$uid}",
            'address' => 'Jl. Test No. 1', 'whatsapp' => '628111000111',
            'latitude' => -6.2, 'longitude' => 106.8, 'is_active' => true, 'radius_meters' => 100,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $uid2 = uniqid();
        $this->employeeId = DB::table('employees')->insertGetId([
            'employee_code' => "EMP-IM-{$uid2}",
            'full_name' => 'Inventory Staff',
            'gender' => 'L',
            'birthdate' => '1985-01-01',
            'email' => "im.{$uid2}@test.com",
            'whatsapp_number' => '628100008888',
            'is_hq' => true,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->boardUser = User::factory()->create(['employee_id' => $this->employeeId]);
        $this->boardUser->assignRole('CEO');

        $this->regularUser = User::factory()->create();
        $this->regularUser->assignRole('HRR');
    }

    private function createItem(array $overrides = []): InventoryItem
    {
        return InventoryItem::create(array_merge([
            'branch_id' => $this->branchId,
            'item_code' => 'ITEM-'.uniqid(),
            'item_name' => 'Test Item',
            'category' => 'furniture',
            'inventory_type' => 'FIXED_ASSET',
            'condition_status' => 'GOOD',
            'quantity' => 5,
            'unit' => 'pcs',
            'location' => 'Ruang Kelas 1',
        ], $overrides));
    }

    // IM-01: create inventory item berhasil
    public function test_can_create_inventory_item(): void
    {
        $this->actingAs($this->boardUser)
            ->post('/facility/inventory', [
                'branch_id' => $this->branchId,
                'item_code' => 'MEJA-001',
                'item_name' => 'Meja Belajar',
                'category' => 'furniture',
                'inventory_type' => 'FIXED_ASSET',
                'condition_status' => 'GOOD',
                'quantity' => 10,
                'unit' => 'pcs',
                'location' => 'Ruang Kelas A',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('inventory_items', [
            'item_name' => 'Meja Belajar',
        ]);
    }

    // IM-20: item_name kosong → 422
    public function test_cannot_create_without_item_name(): void
    {
        $this->actingAs($this->boardUser)
            ->post('/facility/inventory', [
                'branch_id' => $this->branchId,
                'item_code' => 'CODE-001',
                'inventory_type' => 'FIXED_ASSET',
                'condition_status' => 'GOOD',
                'quantity' => 1,
                'unit' => 'pcs',
            ])
            ->assertSessionHasErrors('item_name');
    }

    // IM: tanpa permission → 403
    public function test_user_without_permission_cannot_create_inventory(): void
    {
        $this->actingAs($this->regularUser)
            ->post('/facility/inventory', [
                'branch_id' => $this->branchId,
                'item_code' => 'CODE-001',
                'item_name' => 'Test',
                'inventory_type' => 'FIXED_ASSET',
                'condition_status' => 'GOOD',
                'quantity' => 1,
                'unit' => 'pcs',
            ])
            ->assertStatus(403);
    }

    // IM-24: adjust quantity berhasil
    public function test_can_adjust_inventory_quantity(): void
    {
        $item = $this->createItem(['quantity' => 5]);

        $this->actingAs($this->boardUser)
            ->post("/facility/inventory/{$item->id}/adjust", [
                'quantity' => 8,
                'reason' => 'Tambah dari gudang',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('inventory_items', [
            'id' => $item->id,
            'quantity' => 8,
        ]);
    }

    // IM-26: history di-append per operasi
    public function test_adjust_creates_history_record(): void
    {
        $item = $this->createItem(['quantity' => 5]);

        $this->actingAs($this->boardUser)
            ->post("/facility/inventory/{$item->id}/adjust", [
                'quantity' => 7,
                'reason' => 'Penambahan',
            ]);

        $this->assertDatabaseHas('inventory_item_histories', [
            'inventory_item_id' => $item->id,
            'change_type' => 'quantity_adjusted',
        ]);
    }

    // IM: dispose item berhasil
    public function test_can_dispose_inventory_item(): void
    {
        $item = $this->createItem();

        $this->actingAs($this->boardUser)
            ->post("/facility/inventory/{$item->id}/dispose", [
                'reason' => 'Rusak tidak bisa diperbaiki',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('inventory_items', [
            'id' => $item->id,
            'status' => 'disposed',
        ]);
    }

    // IM: dispose — tanpa permission → 403
    public function test_user_without_dispose_permission_cannot_dispose(): void
    {
        $item = $this->createItem();

        $this->actingAs($this->regularUser)
            ->post("/facility/inventory/{$item->id}/dispose", [
                'reason' => 'Test',
            ])
            ->assertStatus(403);
    }
}
