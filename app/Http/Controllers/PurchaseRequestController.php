<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRequest;
use App\Models\Material;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseRequestController extends Controller
{
    public function index()
    {
        $purchaseRequests = PurchaseRequest::with(['contract', 'requestedBy', 'items.material', 'items.supplier'])
            ->latest()
            ->paginate(10);

        return view('admin.purchase-requests.index', compact('purchaseRequests'));
    }

    public function create()
    {
        $materials = Material::with(['suppliers' => function($query) {
            $query->orderBy('price');
        }])->get();
        
        $suppliers = Supplier::orderBy('company_name')->get();
        $contracts = \App\Models\Contract::with('client')->orderBy('created_at', 'desc')->get();
        $projects = \App\Models\Project::orderBy('created_at', 'desc')->get();
        
        return view('admin.purchase-requests.create', compact('materials', 'suppliers', 'contracts', 'projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'is_project_related' => 'required|boolean',
            'contract_id' => 'required_if:is_project_related,true|exists:contracts,id',
            'project_id' => 'required_if:is_project_related,true|exists:projects,id',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.material_id' => 'required|exists:materials,id',
            'items.*.supplier_id' => 'nullable|exists:suppliers,id',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.unit' => 'required|string',
            'items.*.estimated_unit_price' => 'required|numeric|min:0',
            'items.*.notes' => 'nullable|string',
            'items.*.preferred_brand' => 'nullable|string',
            'items.*.preferred_supplier_id' => 'nullable|exists:suppliers,id'
        ]);

        DB::beginTransaction();
        try {
            $purchaseRequest = new PurchaseRequest([
                'request_number' => 'PR-' . str_pad(PurchaseRequest::count() + 1, 6, '0', STR_PAD_LEFT),
                'contract_id' => $validated['is_project_related'] ? $validated['contract_id'] : null,
                'project_id' => $validated['is_project_related'] ? $validated['project_id'] : null,
                'requested_by' => auth()->id(),
                'status' => 'pending',
                'is_project_related' => $validated['is_project_related'],
                'notes' => $validated['notes']
            ]);

            $totalAmount = 0;

        foreach ($validated['items'] as $item) {
            $purchaseRequest->items()->create([
                'material_id' => $item['material_id'],
                    'supplier_id' => $item['supplier_id'] ?? null,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit' => $item['unit'],
                'estimated_unit_price' => $item['estimated_unit_price'],
                    'total_amount' => $item['quantity'] * $item['estimated_unit_price'],
                    'notes' => $item['notes'] ?? null,
                    'preferred_brand' => $item['preferred_brand'] ?? null,
                    'preferred_supplier_id' => $item['preferred_supplier_id'] ?? null
                ]);

                $totalAmount += $item['quantity'] * $item['estimated_unit_price'];
            }

            $purchaseRequest->total_amount = $totalAmount;
            $purchaseRequest->save();

            DB::commit();

            return redirect()->route('purchase-requests.show', $purchaseRequest)
                ->with('success', 'Purchase request created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating purchase request: ' . $e->getMessage());
        }
    }

    public function show(PurchaseRequest $purchaseRequest)
    {
        $purchaseRequest->load(['contract', 'requestedBy', 'items.material', 'items.supplier', 'items.preferredSupplier']);
        return view('admin.purchase-requests.show', compact('purchaseRequest'));
    }

    public function edit(PurchaseRequest $purchaseRequest)
    {
        if ($purchaseRequest->status !== 'pending') {
            return back()->with('error', 'Cannot edit a purchase request that is not pending.');
        }

        $purchaseRequest->load(['items.material', 'items.supplier']);
        $materials = Material::with(['suppliers' => function($query) {
            $query->orderBy('price');
        }])->get();
        
        $suppliers = Supplier::orderBy('company_name')->get();

        return view('admin.purchase-requests.edit', compact('purchaseRequest', 'materials', 'suppliers'));
    }

    public function update(Request $request, PurchaseRequest $purchaseRequest)
    {
        if ($purchaseRequest->status !== 'pending') {
            return back()->with('error', 'Cannot update a purchase request that is not pending.');
        }

        $validated = $request->validate([
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.material_id' => 'required|exists:materials,id',
            'items.*.supplier_id' => 'nullable|exists:suppliers,id',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.unit' => 'required|string',
            'items.*.estimated_unit_price' => 'required|numeric|min:0',
            'items.*.notes' => 'nullable|string',
            'items.*.preferred_brand' => 'nullable|string',
            'items.*.preferred_supplier_id' => 'nullable|exists:suppliers,id'
        ]);

        DB::beginTransaction();
        try {
        $purchaseRequest->update([
                'notes' => $validated['notes']
            ]);

            // Delete existing items
        $purchaseRequest->items()->delete();

            $totalAmount = 0;

            // Create new items
        foreach ($validated['items'] as $item) {
            $purchaseRequest->items()->create([
                'material_id' => $item['material_id'],
                    'supplier_id' => $item['supplier_id'] ?? null,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit' => $item['unit'],
                'estimated_unit_price' => $item['estimated_unit_price'],
                    'total_amount' => $item['quantity'] * $item['estimated_unit_price'],
                    'notes' => $item['notes'] ?? null,
                    'preferred_brand' => $item['preferred_brand'] ?? null,
                    'preferred_supplier_id' => $item['preferred_supplier_id'] ?? null
                ]);

                $totalAmount += $item['quantity'] * $item['estimated_unit_price'];
            }

            $purchaseRequest->update(['total_amount' => $totalAmount]);

            DB::commit();

            return redirect()->route('purchase-requests.show', $purchaseRequest)
                ->with('success', 'Purchase request updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating purchase request: ' . $e->getMessage());
        }
    }

    public function destroy(PurchaseRequest $purchaseRequest)
    {
        if ($purchaseRequest->status !== 'pending') {
            return back()->with('error', 'Cannot delete a purchase request that is not pending.');
        }

        try {
        $purchaseRequest->delete();
        return redirect()->route('purchase-requests.index')
                ->with('success', 'Purchase request deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting purchase request: ' . $e->getMessage());
        }
    }

    public function approve(PurchaseRequest $purchaseRequest)
    {
        if ($purchaseRequest->status !== 'pending') {
            return back()->with('error', 'Cannot approve a purchase request that is not pending.');
        }

        try {
            $purchaseRequest->update(['status' => 'approved']);
            return back()->with('success', 'Purchase request approved successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error approving purchase request: ' . $e->getMessage());
        }
    }

    public function reject(PurchaseRequest $purchaseRequest)
    {
        if ($purchaseRequest->status !== 'pending') {
            return back()->with('error', 'Cannot reject a purchase request that is not pending.');
        }

        try {
            $purchaseRequest->update(['status' => 'rejected']);
            return back()->with('success', 'Purchase request rejected successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error rejecting purchase request: ' . $e->getMessage());
        }
    }
} 