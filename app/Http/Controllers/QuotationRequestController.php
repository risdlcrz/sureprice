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
                        $pivot = null;
                        $finalizedSupplierId = $quotation->selected_suppliers[$materialId] ?? null;
                        if ($materialId && $finalizedSupplierId) {
                            foreach ($rfqs as $rfq) {
                                $pivot = \DB::table('material_quotation')
                                    ->where('quotation_id', $rfq->id)
                                    ->where('material_id', $materialId)
                                    ->where('selected_supplier_id', $finalizedSupplierId)
                                    ->first();
                                if ($pivot) break;
                            }
                        }
                        \Log::info('Pivot row found', ['pivot' => $pivot]);
                        if ($pivot && $pivot->unit_price) {
                            $unitPrice = $pivot->unit_price;
                            \Log::info('Contract showJson material (DB direct)', [
                                'material_id' => $materialId,
                                'selected_supplier_id' => $finalizedSupplierId,
                                'unit_price' => $unitPrice,
                                'rfq_id' => $pivot->quotation_id ?? null,
                            ]);
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
        // After determining the awarded supplier (awarded_supplier_id), fetch their discount info from the QuotationResponse and include it at the top level of the API response.
        $awardedSupplierId = $quotation->awarded_supplier_id ?? null;
        $awardedDiscount = null;
        if ($awardedSupplierId) {
            // Find the awarded supplier's quotation (RFQ)
            $rfqs = \App\Models\Quotation::where('notes', 'like', '%client quotation request #' . $quotation->request_number . '%')->get();
            foreach ($rfqs as $rfq) {
                $response = \App\Models\QuotationResponse::where('quotation_id', $rfq->id)
                    ->where('supplier_id', $awardedSupplierId)
                    ->first();
                if ($response) {
                    $awardedDiscount = [
                        'discount_type' => $response->discount_type,
                        'discount_percentage' => $response->discount_percentage,
                        'discount_amount' => $response->discount_amount,
                        'final_amount' => $response->final_amount,
                        'total_amount' => $response->total_amount,
                    ];
                    break;
                }
            }
        }
        // Calculate discounted materials cost and grand total AFTER awardedDiscount is set
        $discountedMaterialsCost = $awardedDiscount['final_amount'] ?? $totalMaterialsCost;
        $grandTotal = $discountedMaterialsCost + $laborFee;
        $totalHours = $quotation->total_hours ?? 0;
        // Dynamic duration calculation matching client UI
        $DEFAULT_CREW_SIZE = 8;
        $DEFAULT_HOURS_PER_DAY = 8;
        $totalDays = 0;
        foreach ($quotation->rooms as $room) {
            $length = floatval($room->length ?? 1);
            $width = floatval($room->width ?? 1);
            $height = floatval($room->height ?? 1);
            $floorArea = $length * $width;
            $wallArea = 2 * ($length + $width) * $height;
            foreach ($room->scopes as $scope) {
                $scopeType = $scope->scopeType;
                if (!$scopeType) continue;
                $area = $scopeType->is_wall_work ? $wallArea : $floorArea;
                $laborHoursPerSqm = isset($scopeType->labor_hours_per_sqm) ? floatval($scopeType->labor_hours_per_sqm) : 1;
                $totalLaborHours = $area * $laborHoursPerSqm;
                $days = $totalLaborHours / ($DEFAULT_CREW_SIZE * $DEFAULT_HOURS_PER_DAY);
                $days = ceil($days * 2) / 2; // round up to nearest half day
                $days = max(0.5, $days); // minimum 0.5 days
                \Log::info('Scope dynamic days', [
                    'scope_name' => $scopeType->name,
                    'area' => $area,
                    'labor_hours_per_sqm' => $laborHoursPerSqm,
                    'total_labor_hours' => $totalLaborHours,
                    'days' => $days,
                ]);
                $totalDays += $days;
            }
        }
        $startDate = $quotation->start_date ?? now()->format('Y-m-d');
        $endDate = $totalDays > 0
            ? \Carbon\Carbon::parse($startDate)->addDays(ceil($totalDays) - 1)->format('Y-m-d')
            : $startDate;
        \Log::info('Quotation timeline calculation', [
            'quotation_id' => $quotation->id,
            'total_days' => $totalDays,
            'rooms' => $quotation->rooms ? $quotation->rooms->toArray() : null,
        ]);
        // Debug log for duration calculation
        \Log::info('Contract duration debug', [
            'start_date' => $startDate,
            'total_days' => $totalDays,
            'end_date' => $endDate,
        ]);
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
            'discounted_materials_cost' => $discountedMaterialsCost,
            'labor_fee' => $laborFee,
            'grand_total' => $grandTotal,
            'total_hours' => $totalHours,
            'total_days' => $totalDays,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'awarded_supplier_discount' => $awardedDiscount,
            // Add selected scopes for each room, now including chosen supplier
            'rooms' => $quotation->rooms->map(function($room) use ($quotation, $rfqs) {
                return [
                    'name' => $room->name,
                    'scopes' => $room->scopes->map(function($scope) use ($quotation, $rfqs) {
                        $scopeName = $scope->scopeType->name ?? $scope->scope_name ?? '';
                        $chosenSupplier = 'none selected';
                        $selectedMaterials = [];
                        if (is_array($scope->selected_materials)) {
                            foreach ($scope->selected_materials as $mat) {
                                $materialId = $mat['material_id'] ?? $mat['id'] ?? null;
                                $material = $materialId ? \App\Models\Material::find($materialId) : null;
                                $mat['name'] = $material ? $material->name : ($mat['name'] ?? 'N/A');
                                // Set unit_price to awarded supplier's price if available
                                $finalizedSupplierId = $quotation->selected_suppliers[$materialId] ?? null;
                                $unitPrice = null;
                                $discountType = null;
                                $discountPercentage = null;
                                $discountAmount = null;
                                $finalAmount = null;
                                if ($materialId && $finalizedSupplierId) {
                                    foreach ($rfqs as $rfq) {
                                        $pivot = \DB::table('material_quotation')
                                            ->where('quotation_id', $rfq->id)
                                            ->where('material_id', $materialId)
                                            ->where('selected_supplier_id', $finalizedSupplierId)
                                            ->first();
                                        if ($pivot && $pivot->unit_price) {
                                            $unitPrice = $pivot->unit_price;
                                            // Fetch discount info from QuotationResponse
                                            $response = \App\Models\QuotationResponse::where('quotation_id', $rfq->id)
                                                ->where('supplier_id', $finalizedSupplierId)
                                                ->first();
                                            if ($response) {
                                                $discountType = $response->discount_type;
                                                $discountPercentage = $response->discount_percentage;
                                                $discountAmount = $response->discount_amount;
                                                $finalAmount = $response->final_amount;
                                            }
                                            break;
                                        }
                                    }
                                }
                                if (!$unitPrice && $material) {
                                    $unitPrice = $material->base_price;
                                }
                                $mat['unit_price'] = $unitPrice;
                                $mat['discount_type'] = $discountType;
                                $mat['discount_percentage'] = $discountPercentage;
                                $mat['discount_amount'] = $discountAmount;
                                $mat['final_amount'] = $finalAmount;
                                // --- DEBUG LOGGING ---
                                \Log::info('Supplier lookup debug', [
                                    'material_id' => $materialId,
                                    'material_name' => $mat['name'] ?? null,
                                    'quotation_id' => $quotation->id,
                                ]);
                                // --- Improved supplier lookup ---
                                $pivot = null;
                                $finalizedSupplierId = $quotation->selected_suppliers[$materialId] ?? null;
                                if ($materialId && $finalizedSupplierId) {
                                    foreach ($rfqs as $rfq) {
                                        $pivot = \DB::table('material_quotation')
                                            ->where('quotation_id', $rfq->id)
                                            ->where('material_id', $materialId)
                                            ->where('selected_supplier_id', $finalizedSupplierId)
                                            ->first();
                                        if ($pivot) break;
                                    }
                                }
                                \Log::info('Pivot row found', ['pivot' => $pivot]);
                                $selectedSupplierId = null;
                                $supplierName = 'none selected';
                                if ($pivot && $pivot->selected_supplier_id) {
                                    $selectedSupplierId = $pivot->selected_supplier_id;
                                    $supplier = \App\Models\Supplier::find($selectedSupplierId);
                                    $supplierName = $supplier && $supplier->company_name ? $supplier->company_name : 'none selected';
                                }
                                $mat['supplier_id'] = $selectedSupplierId;
                                $mat['supplier_name'] = $supplierName;
                                // --- END improved supplier lookup ---
                                $selectedMaterials[] = $mat;
                            }
                        }
                        return [
                            'scope_name' => $scopeName,
                            'supplier_name' => $chosenSupplier,
                            'selected_materials' => $selectedMaterials,
                        ];
                    })->values(),
                ];
            })->values(),
        ]);
    }

    /**
     * AJAX search for Select2 dropdown (contracts)
     */
    public function search(Request $request)
    {
        $term = $request->input('q');
        $query = QuotationRequest::doesntHave('contract')->with('user');
        if ($term) {
            $query->where('request_number', 'like', "%$term%")
                  ->orWhereHas('user', function($q) use ($term) {
                      $q->where('name', 'like', "%$term%")
                        ->orWhere('username', 'like', "%$term%")
                        ->orWhere('email', 'like', "%$term%")
                        ;
                  });
        }
        $results = $query->orderByDesc('created_at')->limit(20)->get()->map(function($qr) {
            return [
                'id' => $qr->id,
                'request_number' => $qr->request_number,
                'client_name' => $qr->user->name ?? 'Unknown',
                'created_at' => $qr->created_at ? $qr->created_at->format('Y-m-d') : '',
            ];
        });
        return response()->json(['data' => $results]);
    }
} 