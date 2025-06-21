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

        return view('supplier.quotation-respond', compact('quotation', 'materialsInQuotation', 'existingResponse'));
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

        // Sync response items
        $response->items()->sync($responseItemsData);

        // Update the main response details
        $response->update([
            'total_amount' => $totalQuotedAmount,
            'notes' => $validated['notes'],
            'status' => QuotationResponse::STATUS_SUBMITTED,
        ]);

        // Update the overall quotation status if all invited suppliers have responded
        $totalSuppliers = $quotation->suppliers->count();
        $respondedSuppliers = $quotation->responses()->where('status', QuotationResponse::STATUS_SUBMITTED)->count();

        if ($totalSuppliers === $respondedSuppliers) {
            $quotation->update(['status' => Quotation::STATUS_IN_PROGRESS]); // Or 'responded'
        }

        return redirect()->route('supplier.quotations.show', $quotation)
            ->with('success', 'Quotation response submitted successfully and material prices updated!');
    }
}
