<?php

use App\Models\Category;
use App\Models\Inventory;
use App\Models\Material;
use App\Models\Notification;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequest;
use App\Models\Supplier;
use App\Models\User;

function poUser(string $role, string $type = 'employee'): User
{
    return User::factory()->create(['role' => $role, 'user_type' => $type]);
}

function poFixture(): array
{
    $admin = poUser('admin', 'admin');
    $supplierUser = poUser('supplier', 'company');
    App\Models\Company::create(['user_id' => $supplierUser->id, 'email' => $supplierUser->email, 'username' => $supplierUser->username,
        'company_name' => 'Acme Supply', 'designation' => 'supplier', 'status' => 'approved']);
    $supplier = Supplier::create(['company_name' => 'Acme Supply', 'contact_person' => 'Ann', 'phone' => '0917', 'user_id' => $supplierUser->id, 'email' => 'acme@example.com', 'status' => 'active']);
    $category = Category::create(['name' => 'Lumber', 'slug' => 'lumber']);
    $material = Material::create(['name' => 'Plywood', 'code' => 'PLY-1', 'unit' => 'pcs', 'category_id' => $category->id]);
    $pr = PurchaseRequest::create(['request_number' => 'PR-1', 'requested_by' => $admin->id, 'status' => 'approved']);

    return compact('admin', 'supplierUser', 'supplier', 'material', 'pr');
}

test('a purchase order goes from creation to completion', function () {
    ['admin' => $admin, 'supplierUser' => $supplierUser, 'supplier' => $supplier, 'material' => $material, 'pr' => $pr] = poFixture();
    $warehouse = poUser('warehousing');

    $this->actingAs($admin)->post('/purchase-orders', [
        'purchase_request_id' => $pr->id, 'supplier_id' => $supplier->id,
        'delivery_date' => now()->addWeek()->toDateString(), 'payment_terms' => 'Net 30', 'shipping_terms' => 'FOB',
        'items' => [['material_id' => $material->id, 'quantity' => 10, 'unit_price' => 25, 'specifications' => 'Grade A']],
    ])->assertRedirect();

    $po = PurchaseOrder::firstOrFail();
    expect($po->status)->toBe('pending_payment')
        ->and((float) $po->total_amount)->toBe(250.0)
        ->and($po->items()->first()->notes)->toBe('Grade A');

    // Supplier cannot ship before payment is verified.
    $this->actingAs($supplierUser)->post("/purchase-orders/{$po->id}/ship", ['tracking_number' => 'T1']);
    expect($po->fresh()->status)->toBe('pending_payment');

    $po->update(['status' => 'paid']);
    $this->actingAs($supplierUser)->post("/purchase-orders/{$po->id}/ship", ['tracking_number' => 'T1'])->assertRedirect();
    expect($po->fresh()->status)->toBe('shipping');

    $this->actingAs($supplierUser)->post("/purchase-orders/{$po->id}/receive")->assertForbidden();
    $this->actingAs($warehouse)->post("/purchase-orders/{$po->id}/receive")->assertRedirect();
    expect($po->fresh()->status)->toBe('delivered');

    $this->actingAs($admin)->postJson("/purchase-orders/{$po->id}/complete", [
        'delivery_date' => now()->toDateString(), 'is_on_time' => 1, 'total_units' => 10, 'defective_units' => 0,
    ])->assertOk();

    expect($po->fresh()->status)->toBe('completed')
        ->and((float) Inventory::where('material_id', $material->id)->value('quantity'))->toBe(10.0)
        ->and(Notification::forUser($supplierUser)->count())->toBeGreaterThan(0);
});

test('suppliers cannot open the staff purchase order pages', function () {
    ['supplierUser' => $supplierUser] = poFixture();

    $this->actingAs($supplierUser)->get('/purchase-orders')->assertForbidden();
});

test('only editable purchase orders can be deleted', function () {
    ['admin' => $admin, 'supplier' => $supplier, 'pr' => $pr] = poFixture();
    $base = ['purchase_request_id' => $pr->id, 'supplier_id' => $supplier->id, 'total_amount' => 0,
        'delivery_date' => now()->addWeek(), 'payment_terms' => 'Net 30', 'shipping_terms' => 'FOB'];
    $paid = PurchaseOrder::create($base + ['po_number' => 'PO-A', 'status' => 'paid']);
    $unpaid = PurchaseOrder::create($base + ['po_number' => 'PO-B', 'status' => 'pending_payment']);

    $this->actingAs($admin)->delete("/purchase-orders/{$paid->id}");
    $this->actingAs($admin)->delete("/purchase-orders/{$unpaid->id}");

    expect(PurchaseOrder::find($paid->id))->not->toBeNull()
        ->and(PurchaseOrder::find($unpaid->id))->toBeNull();
});
