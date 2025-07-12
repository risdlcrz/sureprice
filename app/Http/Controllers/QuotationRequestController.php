<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QuotationRequest;

class QuotationRequestController extends Controller
{
    /**
     * Display the specified quotation request as JSON.
     */
    public function showJson($id)
    {
        $quotation = \App\Models\QuotationRequest::with(['user.company', 'rooms.scopes.scopeType.materials'])->findOrFail($id);
        $company = $quotation->user->company;
        $clientAddress = $company
            ? trim("{$company->street}, {$company->barangay}, {$company->city}, {$company->state}, {$company->postal}", ', ')
            : '';
        // Aggregate scope of work and materials
        $scopeOfWork = [];
        $totalMaterialsCost = 0;
        $laborFee = 0;
        // Use selected_suppliers from session if available (from form POST), otherwise use DB
        $selectedSuppliers = session('selected_suppliers') ?? $quotation->selected_suppliers ?? [];
        // Gather all RFQs for this quotation request
        $rfqs = \App\Models\Quotation::where('notes', 'like', '%client quotation request #' . $quotation->request_number . '%')->with(['materials'])->get();
        foreach ($quotation->rooms as $room) {
            foreach ($room->scopes as $scope) {
                $scopeName = $scope->scopeType->name ?? $scope->scope_name ?? '';
                $scopeOfWork[] = $scopeName;
                if (is_array($scope->selected_materials)) {
                    foreach ($scope->selected_materials as $mat) {
                        $materialId = $mat['material_id'] ?? $mat['id'] ?? null;
                        \Log::info('Contract showJson processing material', [
                            'material_id' => $materialId,
                            'selected_suppliers' => $selectedSuppliers,
                        ]);
                        $qty = $mat['quantity'] ?? 1;
                        $unitPrice = 0;
                        // Try to get the selected supplier's quoted price from the pivot
                        if ($materialId && isset($selectedSuppliers[$materialId])) {
                            $selectedSupplierId = $selectedSuppliers[$materialId];
                            foreach (array_reverse($rfqs->all()) as $rfq) {
                                $pivot = \DB::table('material_quotation')
                                    ->where('quotation_id', $rfq->id)
                                    ->where('material_id', $materialId)
                                    ->where('selected_supplier_id', $selectedSupplierId)
                                    ->first();
                                if ($pivot && $pivot->unit_price) {
                                    $unitPrice = $pivot->unit_price;
                                    \Log::info('Contract showJson material (DB direct)', [
                                        'material_id' => $materialId,
                                        'selected_supplier_id' => $selectedSupplierId,
                                        'unit_price' => $unitPrice,
                                        'rfq_id' => $rfq->id,
                                    ]);
                                    break;
                                }
                            }
                        }
                        // Fallback to base price if no supplier price found
                        if (!$unitPrice && $materialId) {
                            $material = \App\Models\Material::find($materialId);
                            $unitPrice = $material ? $material->base_price : 0;
                        }
                        $amount = $unitPrice * $qty;
                        \Log::info('Contract material calculation', [
                            'material_id' => $materialId,
                            'qty' => $qty,
                            'unit_price' => $unitPrice,
                            'amount' => $amount,
                        ]);
                        $totalMaterialsCost += $amount;
                        $laborFee += 0.15 * $amount; // 15% labor fee
                    }
                }
            }
        }
        $grandTotal = $totalMaterialsCost + $laborFee;
        $totalHours = $quotation->total_hours ?? 0;
        $totalDays = $totalHours > 0 ? ceil($totalHours / 8) : 0;
        return response()->json([
            'client' => [
                'name' => $company->company_name ?? $quotation->user->name,
                'street' => $company->street ?? '',
                'barangay' => $company->barangay ?? '',
                'city' => $company->city ?? '',
                'state' => $company->state ?? '',
                'postal' => $company->postal ?? '',
                'address' => $clientAddress,
                'email' => $quotation->user->email,
                'phone' => $company->mobile_number ?? '',
            ],
            'scope_of_work' => implode(', ', array_unique($scopeOfWork)),
            'total_materials_cost' => $totalMaterialsCost,
            'labor_fee' => $laborFee,
            'grand_total' => $grandTotal,
            'total_hours' => $totalHours,
            'total_days' => $totalDays,
            'start_date' => $quotation->start_date ?? null,
            'end_date' => $quotation->end_date ?? null,
        ]);
    }
} 