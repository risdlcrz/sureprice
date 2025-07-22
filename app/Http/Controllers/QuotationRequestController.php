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
            'labor_fee' => $laborFee,
            'grand_total' => $grandTotal,
            'total_hours' => $totalHours,
            'total_days' => $totalDays,
            'start_date' => $startDate,
            'end_date' => $endDate,
            // Add selected scopes for each room, now including chosen supplier
            'rooms' => $quotation->rooms->map(function($room) use ($quotation) {
                return [
                    'name' => $room->name,
                    'scopes' => $room->scopes->map(function($scope) use ($quotation) {
                        $scopeName = $scope->scopeType->name ?? $scope->scope_name ?? '';
                        $chosenSupplier = 'none selected';
                        $selectedMaterials = [];
                        if (is_array($scope->selected_materials)) {
                            foreach ($scope->selected_materials as $mat) {
                                $materialId = $mat['material_id'] ?? $mat['id'] ?? null;
                                $material = $materialId ? \App\Models\Material::find($materialId) : null;
                                $mat['name'] = $material ? $material->name : ($mat['name'] ?? 'N/A');
                                $mat['unit_price'] = $mat['unit_price'] ?? ($material ? $material->base_price : 0);
                                // --- DEBUG LOGGING ---
                                \Log::info('Supplier lookup debug', [
                                    'material_id' => $materialId,
                                    'material_name' => $mat['name'] ?? null,
                                    'quotation_id' => $quotation->id,
                                ]);
                                // --- Improved supplier lookup ---
                                $pivot = null;
                                if ($materialId) {
                                    $pivot = \DB::table('material_quotation')
                                        ->where('quotation_id', $quotation->id)
                                        ->where('material_id', $materialId)
                                        ->whereNotNull('selected_supplier_id')
                                        ->first();
                                }
                                if (!$pivot && !empty($mat['name'])) {
                                    $materialByName = \App\Models\Material::where('name', $mat['name'])->first();
                                    if ($materialByName) {
                                        $pivot = \DB::table('material_quotation')
                                            ->where('quotation_id', $quotation->id)
                                            ->where('material_id', $materialByName->id)
                                            ->whereNotNull('selected_supplier_id')
                                            ->first();
                                    }
                                }
                                $selectedSupplierId = null;
                                $supplierName = 'none selected';
                                if ($pivot) {
                                    $selectedSupplierId = $pivot->selected_supplier_id;
                                    $supplier = \App\Models\Supplier::find($selectedSupplierId);
                                    $supplierName = $supplier ? $supplier->company_name : 'none selected';
                                }
                                // --- DEBUG LOGGING ---
                                \Log::info('Supplier lookup result', [
                                    'pivot' => $pivot,
                                    'selectedSupplierId' => $selectedSupplierId,
                                    'supplierName' => $supplierName,
                                ]);
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