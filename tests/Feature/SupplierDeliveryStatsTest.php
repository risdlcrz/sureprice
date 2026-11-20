<?php

use App\Models\PurchaseOrder;

test('supplier pages compute on-time and late deliveries from completed orders', function () {
    ['supplierUser' => $user, 'supplier' => $supplier, 'pr' => $pr] = poFixture();
    foreach ([[true, 3], [true, 5], [false, 10]] as $i => [$onTime, $days]) {
        $po = PurchaseOrder::forceCreate(['po_number' => "PO-S$i", 'purchase_request_id' => $pr->id, 'supplier_id' => $supplier->id,
            'total_amount' => 100, 'payment_terms' => 'Net 30', 'shipping_terms' => 'FOB', 'status' => 'completed', 'is_on_time' => $onTime, 'delivery_date' => now()->addDays($days)->toDateString()]);
    }
    // an order still in progress doesn't count
    PurchaseOrder::forceCreate(['po_number' => 'PO-OPEN', 'purchase_request_id' => $pr->id, 'supplier_id' => $supplier->id,
        'total_amount' => 100, 'payment_terms' => 'Net 30', 'shipping_terms' => 'FOB', 'status' => 'shipping', 'is_on_time' => false, 'delivery_date' => now()->addDay()->toDateString()]);

    $this->actingAs($user)->get(route('supplier.ranking'))->assertOk()
        ->assertViewHas('completedOrders', 3)
        ->assertViewHas('onTimeRate', 67.0)
        ->assertViewHas('lateDeliveries', 1)
        ->assertViewHas('averageDeliveryTime', 6);
    // supplier.dashboard uses the same on-time rule; its sales chart needs MySQL (YEAR()), so it is not rendered here.
});
