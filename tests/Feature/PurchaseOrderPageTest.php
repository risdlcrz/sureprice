<?php

use App\Models\PurchaseOrder;
use App\Models\PurchaseRequest;
use App\Models\Supplier;
use App\Models\User;

function poPageOrder(string $status): PurchaseOrder
{
    $admin = User::factory()->create(['role' => 'admin', 'user_type' => 'admin']);
    $supplier = Supplier::create(['company_name' => 'Acme', 'contact_person' => 'A', 'phone' => '1', 'email' => uniqid('acme') . '@x.test', 'status' => 'active']);
    $pr = PurchaseRequest::create(['request_number' => uniqid('PR-'), 'requested_by' => $admin->id, 'status' => 'approved']);

    return PurchaseOrder::create(['po_number' => uniqid('PO-'), 'purchase_request_id' => $pr->id, 'supplier_id' => $supplier->id, 'status' => $status,
        'total_amount' => 900, 'delivery_date' => now()->addWeek(), 'payment_terms' => 'Net 30', 'shipping_terms' => 'FOB']);
}

test('admins and finance can submit payment for an unpaid order (it used to require status "approved")', function () {
    $po = poPageOrder('pending_payment');

    foreach ([['admin', 'admin'], ['finance', 'employee']] as [$role, $type]) {
        $this->actingAs(User::factory()->create(['role' => $role, 'user_type' => $type]))->get("/purchase-orders/{$po->id}")
            ->assertOk()->assertSee('Submit Payment to Supplier')->assertSee('id="submitPaymentModal"', false);
    }
    $this->actingAs(User::factory()->create(['role' => 'manager', 'user_type' => 'manager']))->get("/purchase-orders/{$po->id}")
        ->assertOk()->assertDontSee('id="submitPaymentModal"', false)->assertSee('No payment submitted yet.');
});

test('paid orders show no payment form; delivered orders offer completion', function () {
    $admin = User::factory()->create(['role' => 'admin', 'user_type' => 'admin']);

    $this->actingAs($admin)->get('/purchase-orders/' . poPageOrder('paid')->id)
        ->assertOk()->assertDontSee('id="submitPaymentModal"', false);

    PurchaseOrder::query()->delete();
    $this->actingAs($admin)->get('/purchase-orders/' . poPageOrder('delivered')->id)
        ->assertOk()->assertSee('id="completePurchaseOrderModal"', false)->assertSee('Complete Order');
});
