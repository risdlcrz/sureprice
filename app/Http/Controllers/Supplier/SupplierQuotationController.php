<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Quotation;
use App\Models\Material;
use App\Models\QuotationResponse;
use Illuminate\Support\Facades\Auth;

class SupplierQuotationController extends Controller
{
    public function index(Request $request)
    {
        $supplier = Auth::user()->company;
        if (!$supplier || $supplier->designation !== 'supplier') {
            abort(403, 'You are not associated with a supplier account.');
        }
        $supplierId = $supplier->id;

        $query = Quotation::whereHas('suppliers', function ($q) use ($supplierId) {
                $q->where('supplier_id', $supplierId);
            })
            ->with(['materials', 'responses' => function($q) use ($supplierId) {
                $q->where('supplier_id', $supplierId);
            }])
            ->orderBy('created_at', 'desc');

        // You can add filters/search if needed

        $quotations = $query->paginate(10);

        return view('supplier.quotations.index', compact('quotations'));
    }

    public function show(Quotation $quotation)
    {
        $supplier = Auth::user()->company;
        if (!$supplier || $supplier->designation !== 'supplier') {
            abort(403, 'You are not associated with a supplier account.');
        }

        // Ensure the quotation is for the logged-in supplier
        if (!$quotation->suppliers->contains($supplier->id)) {
            abort(403, 'Unauthorized action.');
        }

        // Load materials associated with the quotation
        // If it's a PR-based quotation, materials come from PR items. If standalone, from quotation_material pivot.
        if ($quotation->purchase_request_id) {
            $materialsInQuotation = $quotation->purchaseRequest->items->map(function($item) {
                // Include quantity from PR item as well
                $item->material->requested_quantity = $item->quantity;
                return $item->material;
            });
        } else {
            $materialsInQuotation = $quotation->materials->map(function($material) {
                // Include quantity from quotation_material pivot
                $material->requested_quantity = $material->pivot->quantity;
                return $material;
            });
        }

        // Check if there's an existing response from this supplier for this quotation
        $existingResponse = QuotationResponse::where('quotation_id', $quotation->id)
                                            ->where('supplier_id', $supplier->id)
                                            ->with('items') // Load response items
                                            ->first();

        // Get available discount types and rules
        $discountTypes = QuotationResponse::getAvailableDiscountTypes();
        $discountRules = QuotationResponse::getDiscountRules();

        return view('supplier.quotation-respond', compact('quotation', 'materialsInQuotation', 'existingResponse', 'discountTypes', 'discountRules'));
    }

    public function respond(Request $request, Quotation $quotation)
    {
        $supplier = Auth::user()->company;
        if (!$supplier || $supplier->designation !== 'supplier') {
            abort(403, 'You are not associated with a supplier account.');
        }

        // Ensure the quotation is for the logged-in supplier
        if (!$quotation->suppliers->contains($supplier->id)) {
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
        $response->items()->sync($responseItemsData);

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
