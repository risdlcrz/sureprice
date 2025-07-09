<?php

namespace App\Services;

use App\Models\PurchaseOrder;
use App\Models\Delivery;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PurchaseOrderService
{
    public function validatePaymentByAdmin(PurchaseOrder $purchaseOrder, int $adminId, array $paymentDetails): PurchaseOrder
    {
        if ($purchaseOrder->payment_status !== 'pending') {
            throw new \Exception('Purchase order payment is not pending validation');
        }

        if (!isset($paymentDetails['reference'], $paymentDetails['amount'], $paymentDetails['method'])) {
            throw new \Exception('Payment details are incomplete');
        }

        if (!auth()->user()->hasRole('admin') && !auth()->user()->hasRole('finance')) {
            throw new \Exception('Only administrators or finance can validate client payments.');
        }

        $purchaseOrder->update([
            'admin_payment_validator_id' => $adminId,
            'admin_payment_validated_at' => Carbon::now(),
            'payment_status' => 'pending_supplier_validation',
            'payment_reference' => $paymentDetails['reference'],
            'payment_amount' => $paymentDetails['amount'],
            'payment_method' => $paymentDetails['method'],
            'payment_notes' => $paymentDetails['notes'] ?? null
        ]);

        return $purchaseOrder->fresh();
    }

    public function validatePaymentBySupplier(PurchaseOrder $purchaseOrder, int $supplierId): PurchaseOrder
    {
        if ($purchaseOrder->payment_status !== 'pending_supplier_validation') {
            throw new \Exception('Purchase order payment is not pending supplier validation');
        }

        $purchaseOrder->update([
            'supplier_payment_validator_id' => $supplierId,
            'supplier_payment_validated_at' => Carbon::now(),
            'payment_status' => 'validated',
            'status' => 'processing'
        ]);

        return $purchaseOrder->fresh();
    }

    public function createDelivery(PurchaseOrder $purchaseOrder, int $warehouseId): Delivery
    {
        if ($purchaseOrder->status !== 'processing') {
            throw new \Exception('Purchase order is not in processing state');
        }

        if (!Warehouse::find($warehouseId)) {
            throw new \Exception('Invalid warehouse ID');
        }

        return DB::transaction(function () use ($purchaseOrder, $warehouseId) {
            $delivery = Delivery::create([
                'purchase_order_id' => $purchaseOrder->id,
                'warehouse_id' => $warehouseId,
                'delivery_date' => Carbon::now(),
                'status' => 'pending_confirmation'
            ]);

            foreach ($purchaseOrder->items as $item) {
                $delivery->items()->create([
                    'material_id' => $item->material_id,
                    'quantity' => $item->quantity,
                    'unit' => $item->unit
                ]);
            }

            $purchaseOrder->update(['status' => 'delivered']);

            return $delivery;
        });
    }

    public function confirmDelivery(Delivery $delivery): Delivery
    {
        if ($delivery->status !== 'pending_confirmation') {
            throw new \Exception('Delivery is not pending confirmation');
        }

        return DB::transaction(function () use ($delivery) {
            // Update delivery status
            $delivery->update(['status' => 'confirmed']);

            // Update stock levels for each delivered item
            foreach ($delivery->items as $item) {
                $stock = Stock::firstOrCreate(
                    [
                        'warehouse_id' => $delivery->warehouse_id,
                        'material_id' => $item->material_id
                    ],
                    ['current_stock' => 0]
                );

                // Create stock movement record
                StockMovement::create([
                    'warehouse_id' => $delivery->warehouse_id,
                    'material_id' => $item->material_id,
                    'quantity' => $item->quantity,
                    'type' => 'in',
                    'reference_type' => 'delivery',
                    'reference_id' => $delivery->id
                ]);

                // Update stock level
                $stock->increment('current_stock', $item->quantity);
            }

            // Update purchase order status
            $delivery->purchaseOrder->update(['status' => 'completed']);

            return $delivery->fresh();
        });
    }
} 