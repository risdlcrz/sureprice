<?php

use App\Models\Category;
use App\Models\Inventory;
use App\Models\Material;
use App\Models\Stock;
use App\Models\User;
use App\Models\Warehouse;

function invMaterial(string $name, float $minimum, array $stockByWarehouse): array
{
    $category = Category::firstOrCreate(['slug' => 'masonry'], ['name' => 'Masonry']);
    $material = Material::create(['name' => $name, 'code' => uniqid('M'), 'unit' => 'bags', 'category_id' => $category->id, 'minimum_stock' => $minimum]);
    foreach ($stockByWarehouse as $warehouseId => $qty) {
        Stock::create(['warehouse_id' => $warehouseId, 'material_id' => $material->id, 'current_stock' => $qty, 'threshold' => 0]);
    }
    $inventory = Inventory::create(['material_id' => $material->id, 'quantity' => array_sum($stockByWarehouse), 'unit' => 'bags', 'status' => 'active']);

    return [$material, $inventory];
}

test('the inventory page lists stock per warehouse with status pills and row actions', function () {
    $a = Warehouse::create(['name' => 'Warehouse A']);
    $b = Warehouse::create(['name' => 'Warehouse B']);
    [$cementMaterial] = invMaterial('Portland Cement', 10, [$a->id => 40, $b->id => 12.5]);
    // The page acts on the material's first inventory record (stock changes may create one automatically).
    $cement = $cementMaterial->inventory()->first();
    invMaterial('Hollow Blocks', 100, [$a->id => 20]);
    invMaterial('Rebar 10mm', 5, []);

    $admin = User::factory()->create(['role' => 'admin', 'user_type' => 'employee']);

    $this->actingAs($admin)->get('/inventory')->assertOk()
        // shortened warehouse headers keep the full name in the tooltip
        ->assertSee('title="Warehouse A stock"', false)->assertSeeText('WH A')->assertSeeText('WH B')
        ->assertSee('<td data-label="Total" class="inv-num fw-semibold">52.5</td>', false)
        ->assertSee('inv-pill--active', false)->assertSee('inv-pill--low', false)->assertSee('inv-pill--out', false)
        // restock / edit / delete are separate buttons; delete opens a confirm modal that targets the right record
        ->assertSee('title="Add / restock"', false)->assertSee('title="Edit"', false)
        ->assertSee('data-bs-target="#deleteInventory' . $cement->id . '"', false)
        ->assertSee('action="' . route('inventory.destroy', $cement) . '"', false)
        ->assertSeeText('Expiring Soon')->assertSeeText('Add New Item');
});

test('the inventory page shows an empty state when nothing is stocked', function () {
    $admin = User::factory()->create(['role' => 'admin', 'user_type' => 'employee']);

    $this->actingAs($admin)->get('/inventory')->assertOk()->assertSeeText('No inventory items yet.');
});
