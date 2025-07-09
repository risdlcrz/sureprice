<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Services\MaterialRequestService;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ContractWorkflowController extends Controller
{
    protected $materialRequestService;
    protected $stockService;

    public function __construct(MaterialRequestService $materialRequestService, StockService $stockService)
    {
        $this->materialRequestService = $materialRequestService;
        $this->stockService = $stockService;
    }

    public function checkStock(Contract $contract)
    {
        try {
            $stockResults = $this->stockService->checkStockForContract($contract);
            
            $contract->update([
                'stock_check_results' => $stockResults,
                'stock_checked_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Stock check completed successfully',
                'results' => $stockResults
            ]);
        } catch (\Exception $e) {
            Log::error('Stock check failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to check stock'
            ], 500);
        }
    }

    public function adminApprove(Contract $contract)
    {
        try {
            if (!Auth::user()->hasRole('admin')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized action'
                ], 403);
            }

            $contract->update([
                'admin_approval_status' => 'approved',
                'admin_approved_at' => now(),
                'admin_approved_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Contract approved by admin'
            ]);
        } catch (\Exception $e) {
            Log::error('Admin approval failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve contract'
            ], 500);
        }
    }

    public function supplierApprove(Contract $contract)
    {
        try {
            if (!Auth::user()->hasRole('supplier')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized action'
                ], 403);
            }

            if (!$contract->isAdminApproved()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Admin approval required first'
                ], 400);
            }

            $contract->update([
                'supplier_approval_status' => 'approved',
                'supplier_approved_at' => now(),
                'supplier_approved_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Contract approved by supplier'
            ]);
        } catch (\Exception $e) {
            Log::error('Supplier approval failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve contract'
            ], 500);
        }
    }

    public function validatePaymentAdmin(Contract $contract)
    {
        try {
            if (!Auth::user()->hasRole('admin') && !Auth::user()->hasRole('finance')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized action'
                ], 403);
            }

            $contract->update([
                'admin_payment_validated_at' => now(),
                'admin_payment_validator_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment validated by admin'
            ]);
        } catch (\Exception $e) {
            Log::error('Admin payment validation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to validate payment'
            ], 500);
        }
    }

    public function validatePaymentSupplier(Contract $contract)
    {
        try {
            if (!Auth::user()->hasRole('supplier')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized action'
                ], 403);
            }

            if (!$contract->admin_payment_validated_at) {
                return response()->json([
                    'success' => false,
                    'message' => 'Admin payment validation required first'
                ], 400);
            }

            $contract->update([
                'supplier_payment_validated_at' => now(),
                'supplier_payment_validator_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment validated by supplier'
            ]);
        } catch (\Exception $e) {
            Log::error('Supplier payment validation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to validate payment'
            ], 500);
        }
    }

    public function createDelivery(Contract $contract)
    {
        try {
            if (!$contract->isPaymentValidated()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment validation required first'
                ], 400);
            }

            $contract->update([
                'delivery_status' => 'pending_confirmation',
                'delivery_created_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Delivery created successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Delivery creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create delivery'
            ], 500);
        }
    }

    public function confirmDelivery(Contract $contract)
    {
        try {
            if ($contract->delivery_status !== 'pending_confirmation') {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid delivery status'
                ], 400);
            }

            $contract->update([
                'delivery_status' => 'confirmed',
                'delivery_confirmed_at' => now(),
                'delivery_confirmed_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Delivery confirmed successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Delivery confirmation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to confirm delivery'
            ], 500);
        }
    }

    public function updateStock(Contract $contract)
    {
        try {
            if (!$contract->isDeliveryConfirmed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Delivery confirmation required first'
                ], 400);
            }

            $this->stockService->updateStockForContract($contract);

            $contract->update([
                'stock_updated' => true,
                'stock_updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Stock updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Stock update failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update stock'
            ], 500);
        }
    }
} 