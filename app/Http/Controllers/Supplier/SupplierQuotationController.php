<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Quotation;
use App\Models\Material;
use App\Models\QuotationResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Notification;

class SupplierQuotationController extends Controller
{
    public function index(Request $request)
    {
        $supplier = Auth::user()->supplier;
        if (!$supplier) {
            abort(403, 'You are not associated with a supplier account.');
        }
        $supplierId = $supplier->id;

        $query = Quotation::whereHas('suppliers', function ($q) use ($supplierId) {
                $q->where('supplier_id', $supplierId);
            })
            ->with(['materials', 'responses' => function($q) use ($supplierId) {
                $q->where('supplier_id', $supplierId);
            }, 'purchaseRequest', 'suppliers'])
            ->orderBy('created_at', 'desc');

        // Add filters for status, sort, and per page
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');
        $query->orderBy($sort, $direction);
        $perPage = $request->input('perPage', 10);
        $quotations = $query->paginate($perPage);

        return view('supplier.quotations.index', compact('quotations'));
    }

    public function show(Quotation $quotation)
    {
        $supplier = Auth::user()->supplier;
        if (!$supplier) {
            abort(403, 'You are not associated with a supplier account.');
        }
        if (!$quotation->suppliers->contains('id', $supplier->id)) {
            abort(403, 'Unauthorized action.');
        }
        // Try to extract client quotation request number from notes
        $quotationRequest = null;
        $materialQuantities = [];
        if (preg_match('/client quotation request #([A-Za-z0-9\-]+)/i', $quotation->notes, $matches)) {
            $requestNumber = $matches[1];
            $quotationRequest = \App\Models\QuotationRequest::where('request_number', $requestNumber)
                ->with(['rooms.scopes'])
                ->first();
            \Log::info('QuotationRequest:', ['id' => $quotationRequest?->id, 'rooms_count' => $quotationRequest?->rooms?->count()]);
            if ($quotationRequest) {
                foreach ($quotationRequest->rooms as $room) {
                    \Log::info('Room:', ['id' => $room->id, 'scopes_count' => $room->scopes?->count()]);
                    foreach ($room->scopes as $scope) {
                        \Log::info('Scope selected_materials:', ['scope_id' => $scope->id, 'selected_materials' => $scope->selected_materials]);
                        $selectedMaterials = $scope->selected_materials;
                        if (is_string($selectedMaterials)) {
                            $selectedMaterials = json_decode($selectedMaterials, true);
                        }
                        if (is_array($selectedMaterials)) {
                            foreach ($selectedMaterials as $mat) {
                                // FIX: Use 'material_id' instead of 'id'
                                if (isset($mat['material_id']) && isset($mat['quantity'])) {
                                    $materialQuantities[$mat['material_id']] = $mat['quantity'];
                                }
                            }
                        }
                    }
                }
            }
        }
        \Log::info('Material Quantities Map:', $materialQuantities);
        \Log::info('Quotation Materials:', $quotation->materials->pluck('id')->toArray());
        $materialsInQuotation = $quotation->materials->map(function($material) use ($materialQuantities, $supplier) {
            $material->requested_quantity = $materialQuantities[$material->id] ?? 1;
            // Fetch the supplier's price from the pivot table
            $pivot = \DB::table('material_supplier')
                ->where('material_id', $material->id)
                ->where('supplier_id', $supplier->id)
                ->first();
            $material->price = $pivot ? $pivot->price : 0;
            return $material;
        });
        $existingResponse = QuotationResponse::where('quotation_id', $quotation->id)
                                            ->where('supplier_id', $supplier->id)
                                            ->with('items')
                                            ->first();
        $discountTypes = QuotationResponse::getAvailableDiscountTypes();
        $discountRules = QuotationResponse::getDiscountRules();
        return view('supplier.quotation-respond', compact('quotation', 'materialsInQuotation', 'existingResponse', 'discountTypes', 'discountRules', 'quotationRequest'));
    }

    public function respond(Request $request, Quotation $quotation)
    {
        $supplier = Auth::user()->supplier;
        if (!$supplier) {
            abort(403, 'You are not associated with a supplier account.');
        }

        // Ensure the quotation is for the logged-in supplier
        if (!$quotation->suppliers->contains('id', $supplier->id)) {
            abort(403, 'Unauthorized action.');
        }

        // Validate the response
        $validated = $request->validate([
            'notes' => 'nullable|string|max:1000',
            'materials' => 'required|array',
            'materials.*.unit_price' => 'required|numeric|min:0',
            'materials.*.quantity' => 'required|numeric|min:0.01', // Quantity is passed from the view as hidden input
            'discount_type' => 'nullable|string|in:' . implode(',', array_keys(QuotationResponse::getAvailableDiscountTypes())),
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'discount_reason' => 'nullable|string|max:500',
            'payment_terms' => 'nullable|string|max:200',
            'delivery_terms' => 'nullable|string|max:200',
            'validity_period' => 'nullable|string|max:100',
        ]);

        // Find or create a quotation response for this supplier
        $response = QuotationResponse::firstOrCreate(
            [
                'quotation_id' => $quotation->id,
                'supplier_id' => $supplier->id,
            ],
            [
                'status' => QuotationResponse::STATUS_PENDING, // Default status for new response
                'total_amount' => 0, // Ensure this is set to avoid SQL error
            ]
        );

        $totalQuotedAmount = 0;
        $responseItemsData = [];

        // Process each material's response
        foreach ($validated['materials'] as $materialId => $materialData) {
            $unitPrice = $materialData['unit_price'];
            $quantity = $materialData['quantity'];
            $totalPrice = $unitPrice * $quantity;
            $totalQuotedAmount += $totalPrice;

            $responseItemsData[$materialId] = [
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_price' => $totalPrice,
                // 'specifications' => '', // Add if needed in future
                // 'notes' => '', // Add if needed in future
            ];

            // Update the material's price for this supplier
            $material = Material::find($materialId);
            if ($material) {
                // Update the material's price in supplier's inventory
                // Assuming 'price' is the field for supplier's selling price
                $material->update(['price' => $unitPrice]);
            }
        }

        // Calculate discount and final amount
        $discountPercentage = 0;
        $discountAmount = 0;
        $finalAmount = $totalQuotedAmount;
        $discountType = $validated['discount_type'] ?? QuotationResponse::DISCOUNT_TYPE_NONE;

        // Apply discount based on type
        if ($discountType !== QuotationResponse::DISCOUNT_TYPE_NONE) {
            if (isset($validated['discount_percentage']) && $validated['discount_percentage'] > 0) {
                $discountPercentage = $validated['discount_percentage'];
                $discountAmount = ($totalQuotedAmount * $discountPercentage) / 100;
            } elseif (isset($validated['discount_amount']) && $validated['discount_amount'] > 0) {
                $discountAmount = $validated['discount_amount'];
                if ($discountAmount > $totalQuotedAmount) {
                    $discountAmount = $totalQuotedAmount; // Don't allow negative final amount
                }
                $discountPercentage = $totalQuotedAmount > 0 ? ($discountAmount / $totalQuotedAmount) * 100 : 0;
            }

            $finalAmount = $totalQuotedAmount - $discountAmount;
        }

        // Sync response items
        $response->items()->delete();
        foreach ($responseItemsData as $materialId => $itemData) {
            $itemData['material_id'] = $materialId;
            $response->items()->create($itemData);
        }

        // Update the main response details
        $response->update([
            'total_amount' => $totalQuotedAmount,
            'discount_type' => $discountType,
            'discount_percentage' => $discountPercentage,
            'discount_amount' => $discountAmount,
            'final_amount' => $finalAmount,
            'discount_reason' => $validated['discount_reason'] ?? null,
            'payment_terms' => $validated['payment_terms'] ?? null,
            'delivery_terms' => $validated['delivery_terms'] ?? null,
            'validity_period' => $validated['validity_period'] ?? null,
            'notes' => $validated['notes'],
            'status' => QuotationResponse::STATUS_SUBMITTED,
        ]);

        // Notify all admins about the new supplier response
        $admins = User::role('admin')->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type' => 'supplier_quotation_response',
                'notifiable_type' => Quotation::class,
                'notifiable_id' => $quotation->id,
                'data' => [
                    'title' => 'Supplier Quotation Response Submitted',
                    'message' => 'A supplier has submitted a response to RFQ #' . $quotation->rfq_number . '.',
                    'link' => route('quotations.show', $quotation->id),
                ],
                'for_role' => 'admin',
            ]);
        }

        // Validate discount rules
        $discountErrors = $response->validateDiscount();
        if (!empty($discountErrors)) {
            return back()->withInput()->withErrors(['discount' => $discountErrors]);
        }

        // Update the overall quotation status if all invited suppliers have responded
        $totalSuppliers = $quotation->suppliers->count();
        $respondedSuppliers = $quotation->responses()->where('status', QuotationResponse::STATUS_SUBMITTED)->count();

        if ($totalSuppliers === $respondedSuppliers) {
            $quotation->update(['status' => Quotation::STATUS_IN_PROGRESS]); // Or 'responded'
        }

        $successMessage = 'Quotation response submitted successfully and material prices updated!';
        if ($discountAmount > 0) {
            $successMessage .= ' ' . $response->discount_type_display . ' of ' . ($discountPercentage > 0 ? $discountPercentage . '%' : '₱' . number_format($discountAmount, 2)) . ' applied.';
        }

        return redirect()->route('supplier.quotations.show', $quotation)
            ->with('success', $successMessage);
    }

    public function getDiscountInfo(Request $request)
    {
        $discountType = $request->input('discount_type');
        $orderAmount = $request->input('order_amount', 0);
        
        $rules = QuotationResponse::DISCOUNT_RULES[$discountType] ?? null;
        
        if (!$rules) {
            return response()->json(['error' => 'Invalid discount type']);
        }

        $isEligible = $orderAmount >= $rules['min_order_amount'];
        
        return response()->json([
            'description' => $rules['description'],
            'max_percentage' => $rules['max_percentage'],
            'min_order_amount' => $rules['min_order_amount'],
            'is_eligible' => $isEligible,
            'message' => $isEligible ? 
                "You are eligible for up to {$rules['max_percentage']}% discount." : 
                "Minimum order amount of ₱" . number_format($rules['min_order_amount'], 2) . " required for this discount type."
        ]);
    }
}
