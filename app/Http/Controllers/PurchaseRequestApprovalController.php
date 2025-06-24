<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseRequestApprovalController extends Controller
{
    public function adminApprove(Request $request, PurchaseRequest $purchaseRequest)
    {
        try {
            $purchaseRequest->approveByAdmin();
            
            return response()->json([
                'success' => true,
                'message' => 'Purchase request approved by admin successfully.',
                'is_fully_approved' => $purchaseRequest->isFullyApproved()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 403);
        }
    }

    public function supplierApprove(Request $request, PurchaseRequest $purchaseRequest)
    {
        try {
            $purchaseRequest->approveBySupplier();
            
            return response()->json([
                'success' => true,
                'message' => 'Purchase request approved by supplier successfully.',
                'is_fully_approved' => $purchaseRequest->isFullyApproved()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 403);
        }
    }

    public function getApprovalStatus(PurchaseRequest $purchaseRequest)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'admin_approved' => $purchaseRequest->admin_approved,
                'admin_approved_at' => $purchaseRequest->admin_approved_at,
                'admin_approver' => $purchaseRequest->adminApprover ? $purchaseRequest->adminApprover->name : null,
                'supplier_approved' => $purchaseRequest->supplier_approved,
                'supplier_approved_at' => $purchaseRequest->supplier_approved_at,
                'supplier_approver' => $purchaseRequest->supplierApprover ? $purchaseRequest->supplierApprover->name : null,
                'is_fully_approved' => $purchaseRequest->isFullyApproved(),
                'status' => $purchaseRequest->status
            ]
        ]);
    }
} 