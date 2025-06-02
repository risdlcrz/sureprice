<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\PurchaseRequest;
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
        return view('admin.purchase-requests.form', compact('contracts'));
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
            'notes' => 'nullable|string',
            'attachments.*' => 'nullable|file|max:10240'
        ]);

        $purchaseRequest = PurchaseRequest::create([
            'contract_id' => $validated['contract_id'],
            'pr_number' => 'PR-' . Str::random(8),
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
                'quantity' => $item['quantity'],
                'specifications' => $item['specifications'] ?? null
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

        return redirect()->route('purchase-request.index')
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
        $purchaseRequest->load(['contract', 'items.material', 'attachments']);
        return view('admin.purchase-requests.form', compact('purchaseRequest', 'contracts'));
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
                'quantity' => $item['quantity'],
                'specifications' => $item['specifications'] ?? null
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

        return redirect()->route('purchase-request.index')
            ->with('success', 'Purchase request updated successfully');
    }

    public function destroy(PurchaseRequest $purchaseRequest)
    {
        $purchaseRequest->delete();
        return redirect()->route('purchase-request.index')
            ->with('success', 'Purchase request deleted successfully');
    }
} 