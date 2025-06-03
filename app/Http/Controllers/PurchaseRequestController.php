<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\PurchaseRequest;
use App\Models\Material;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PurchaseRequestController extends Controller
{
    public function index()
    {
        $purchaseRequests = PurchaseRequest::with(['contract', 'requester'])
            ->latest()
            ->paginate(10);
            
        return view('admin.purchase-requests.index', compact('purchaseRequests'));
    }

    public function create()
    {
        $contracts = Contract::with(['client', 'contractor'])
            ->where('status', 'approved')
            ->orderBy('contract_id')
            ->get();
        $materials = Material::orderBy('name')->get();
        $suppliers = Supplier::all();
        return view('admin.purchase-requests.form', compact('contracts', 'materials', 'suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'contract_id' => 'required|exists:contracts,id',
            'department' => 'required|string|max:255',
            'required_date' => 'required|date|after:today',
            'purpose' => 'required|string',
            'items' => 'required|array',
            'items.*.material_id' => 'required|exists:materials,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.specifications' => 'nullable|string',
            'items.*.description' => 'required|string',
            'items.*.unit' => 'required|string',
            'items.*.estimated_unit_price' => 'required|numeric|min:0',
            'items.*.total_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'attachments.*' => 'nullable|file|max:10240'
        ]);

        // Get the last PR number
        $lastPR = \App\Models\PurchaseRequest::orderBy('id', 'desc')->first();
        if ($lastPR && preg_match('/pr-(\\d+)/i', $lastPR->pr_number, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }
        $prNumber = 'pr-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        $purchaseRequest = PurchaseRequest::create([
            'contract_id' => $validated['contract_id'],
            'pr_number' => $prNumber,
            'requester_id' => auth()->id(),
            'department' => $validated['department'],
            'required_date' => $validated['required_date'],
            'purpose' => $validated['purpose'],
            'notes' => $validated['notes'] ?? null,
            'status' => 'draft'
        ]);

        foreach ($validated['items'] as $item) {
            $purchaseRequest->items()->create([
                'material_id' => $item['material_id'],
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit' => $item['unit'],
                'estimated_unit_price' => $item['estimated_unit_price'],
                'total_amount' => $item['total_amount'],
                'notes' => $item['notes'] ?? null
            ]);
        }

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('purchase-requests/' . $purchaseRequest->id);
                $purchaseRequest->attachments()->create([
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName()
                ]);
            }
        }

        return redirect()->route('purchase-requests.index')
            ->with('success', 'Purchase request created successfully');
    }

    public function show(PurchaseRequest $purchaseRequest)
    {
        $purchaseRequest->load(['contract', 'requester', 'items.material', 'attachments']);
        return view('admin.purchase-requests.show', compact('purchaseRequest'));
    }

    public function edit(PurchaseRequest $purchaseRequest)
    {
        $contracts = Contract::with(['client', 'contractor'])
            ->where('status', 'approved')
            ->orderBy('contract_id')
            ->get();
        $materials = Material::orderBy('name')->get();
        $purchaseRequest->load(['contract', 'items.material', 'attachments']);
        return view('admin.purchase-requests.form', compact('purchaseRequest', 'contracts', 'materials'));
    }

    public function update(Request $request, PurchaseRequest $purchaseRequest)
    {
        $validated = $request->validate([
            'contract_id' => 'required|exists:contracts,id',
            'department' => 'required|string|max:255',
            'required_date' => 'required|date|after:today',
            'purpose' => 'required|string',
            'items' => 'required|array',
            'items.*.material_id' => 'required|exists:materials,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.specifications' => 'nullable|string',
            'items.*.description' => 'required|string',
            'items.*.unit' => 'required|string',
            'items.*.estimated_unit_price' => 'required|numeric|min:0',
            'items.*.total_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'attachments.*' => 'nullable|file|max:10240'
        ]);

        $purchaseRequest->update([
            'contract_id' => $validated['contract_id'],
            'department' => $validated['department'],
            'required_date' => $validated['required_date'],
            'purpose' => $validated['purpose'],
            'notes' => $validated['notes'] ?? null
        ]);

        // Sync items
        $purchaseRequest->items()->delete();
        foreach ($validated['items'] as $item) {
            $purchaseRequest->items()->create([
                'material_id' => $item['material_id'],
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit' => $item['unit'],
                'estimated_unit_price' => $item['estimated_unit_price'],
                'total_amount' => $item['total_amount'],
                'notes' => $item['notes'] ?? null
            ]);
        }

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('purchase-requests/' . $purchaseRequest->id);
                $purchaseRequest->attachments()->create([
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName()
                ]);
            }
        }

        return redirect()->route('purchase-requests.index')
            ->with('success', 'Purchase request updated successfully');
    }

    public function destroy(PurchaseRequest $purchaseRequest)
    {
        $purchaseRequest->delete();
        return redirect()->route('purchase-requests.index')
            ->with('success', 'Purchase request deleted successfully');
    }

    public function getItems(PurchaseRequest $purchaseRequest)
    {
        return response()->json($purchaseRequest->items()->with('material')->get());
    }

    public function updateStatus(Request $request, PurchaseRequest $purchaseRequest)
    {
        $request->validate([
            'status' => 'required|in:draft,approved,rejected'
        ]);

        $purchaseRequest->status = $request->status;
        $purchaseRequest->save();

        return redirect()->route('purchase-requests.show', $purchaseRequest)
            ->with('success', 'Purchase request status updated to ' . ucfirst($request->status));
    }
} 