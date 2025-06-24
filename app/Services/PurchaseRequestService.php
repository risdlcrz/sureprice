<?php

namespace App\Services;

use App\Models\PurchaseRequest;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PurchaseRequestService
{
    public function approveByAdmin(PurchaseRequest $purchaseRequest, int $adminId): PurchaseRequest
    {
        if ($purchaseRequest->status !== 'pending_admin_approval') {
            throw new \Exception('Purchase request is not pending admin approval');
        }

        $purchaseRequest->update([
            'admin_id' => $adminId,
            'admin_approved_at' => now(),
            'status' => 'pending_supplier_approval'
        ]);

        return $purchaseRequest->fresh();
    }

    public function approveBySupplier(PurchaseRequest $purchaseRequest, int $supplierId): PurchaseOrder
    {
        if ($purchaseRequest->status !== 'pending_supplier_approval') {
            throw new \Exception('Purchase request is not pending supplier approval');
        }

        return DB::transaction(function () use ($purchaseRequest, $supplierId) {
            // Update purchase request
            $purchaseRequest->update([
                'supplier_id' => $supplierId,
                'supplier_approved_at' => now(),
                'status' => 'approved'
            ]);

            // Create purchase order
            $purchaseOrder = PurchaseOrder::create([
                'purchase_request_id' => $purchaseRequest->id,
                'po_number' => 'PO-' . date('YmdHis'),
                'supplier_id' => $supplierId,
                'status' => 'pending_payment',
                'payment_status' => 'pending',
                'total_amount' => $purchaseRequest->items->sum('total_amount'),
                'delivery_date' => Carbon::now()->addDays(7),
                'terms_and_conditions' => 'Standard terms apply'
            ]);

            // Create purchase order items
            foreach ($purchaseRequest->items as $item) {
                $purchaseOrder->items()->create([
                    'material_id' => $item->material_id,
                    'description' => $item->description,
                    'quantity' => $item->quantity,
                    'unit' => $item->unit,
                    'unit_price' => $item->estimated_unit_price,
                    'total_amount' => $item->total_amount
                ]);
            }

            return $purchaseOrder;
        });
    }

    public function reject(PurchaseRequest $purchaseRequest, int $rejectedBy, string $reason): PurchaseRequest
    {
        if (!in_array($purchaseRequest->status, ['pending_admin_approval', 'pending_supplier_approval'])) {
            throw new \Exception('Purchase request cannot be rejected in its current state');
        }

        $updateData = [
            'status' => 'rejected',
            'rejection_reason' => $reason
        ];

        if ($purchaseRequest->status === 'pending_admin_approval') {
            $updateData['admin_id'] = $rejectedBy;
        } else {
            $updateData['supplier_id'] = $rejectedBy;
        }

        $purchaseRequest->update($updateData);

        return $purchaseRequest->fresh();
    }
} 