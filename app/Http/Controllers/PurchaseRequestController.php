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
    public function index(Request $request)
    {
        $query = PurchaseRequest::with(['contract', 'requester']);

        // Handle clear filter
        if ($request->has('clear')) {
            return redirect()->route('purchase-requests.index');
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('pr_number', 'like', "%$search%")
                  ->orWhere('department', 'like', "%$search%")
                  ->orWhere('purpose', 'like', "%$search%")
                  ->orWhereHas('contract', function($q2) use ($search) {
                      $q2->where('contract_id', 'like', "%$search%")
                         ->orWhere('status', 'like', "%$search%")
                         ->orWhereHas('client', function($q3) use ($search) {
                             $q3->where('company_name', 'like', "%$search%")
                                ->orWhere('name', 'like', "%$search%")
                         ;});
                  });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $purchaseRequests = $query->latest()->paginate(10)->withQueryString();

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
            'contract_id' => 'nullable|exists:contracts,id',
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
            'contract_id' => $validated['contract_id'] ?? null,
            'pr_number' => $prNumber,
            'requester_id' => auth()->id(),
            'department' => $validated['department'],
            'required_date' => $validated['required_date'],
            'purpose' => $validated['purpose'],
            'notes' => $validated['notes'] ?? null,
            'status' => 'draft'
        ]);

        foreach ($validated['items'] as $item) {
            $supplierId = $item['supplier_id'] ?? null;
            if (!$supplierId) {
                $material = Material::find($item['material_id']);
                $supplier = $material
                    ? ($material->suppliers()->wherePivot('is_preferred', true)->first() ?? $material->suppliers()->first())
                    : null;
                $supplierId = $supplier ? $supplier->id : null;
            }

            $purchaseRequest->items()->create([
                'material_id' => $item['material_id'],
                'supplier_id' => $supplierId,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit' => $item['unit'],
                'estimated_unit_price' => $item['estimated_unit_price'],
                'total_amount' => $item['total_amount'],
                'specifications' => $item['specifications'] ?? null,
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
            'contract_id' => 'nullable|exists:contracts,id',
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
            'contract_id' => $validated['contract_id'] ?? null,
            'department' => $validated['department'],
            'required_date' => $validated['required_date'],
            'purpose' => $validated['purpose'],
            'notes' => $validated['notes'] ?? null
        ]);

        // Sync items
        $purchaseRequest->items()->delete();
        foreach ($validated['items'] as $item) {
            $supplierId = $item['supplier_id'] ?? null;
            if (!$supplierId) {
                // Auto-select preferred supplier or first supplier for the material
                $material = Material::find($item['material_id']);
                $supplier = $material
                    ? ($material->suppliers()->wherePivot('is_preferred', true)->first() ?? $material->suppliers()->first())
                    : null;
                $supplierId = $supplier ? $supplier->id : null;
            }

            $purchaseRequest->items()->create([
                'material_id' => $item['material_id'],
                'supplier_id' => $supplierId,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit' => $item['unit'],
                'estimated_unit_price' => $item['estimated_unit_price'],
                'total_amount' => $item['total_amount'],
                'specifications' => $item['specifications'] ?? null,
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

    public function generateFromContract(Request $request)
    {
        $data = $request->validate([
            'contract_id' => 'required|integer|exists:contracts,id',
            'items' => 'required|array',
            'items.*.name' => 'required|string',
            'items.*.unit' => 'required|string',
            'items.*.unitCost' => 'required|numeric',
            'items.*.quantity' => 'required|numeric',
            'items.*.totalCost' => 'required|numeric',
        ]);

        // Generate PR number (e.g., PR-YYYYMMDD-XXXX)
        $date = now()->format('Ymd');
        $lastPR = \App\Models\PurchaseRequest::where('pr_number', 'like', "PR-{$date}-%")
            ->orderBy('pr_number', 'desc')
            ->first();
        $sequence = '0001';
        if ($lastPR) {
            $lastSequence = intval(substr($lastPR->pr_number, -4));
            $sequence = str_pad($lastSequence + 1, 4, '0', STR_PAD_LEFT);
        }
        $prNumber = "PR-{$date}-{$sequence}";

        $contract = \App\Models\Contract::findOrFail($data['contract_id']);

        $purchaseRequest = \App\Models\PurchaseRequest::create([
            'contract_id' => $contract->id,
            'pr_number' => $prNumber,
            'status' => 'draft',
            'requester_id' => auth()->id() ?? 1,
            'department' => 'Procurement',
            'required_date' => $contract->start_date ?? now()->addWeek(),
            'purpose' => 'Materials procurement for Contract ' . ($contract->contract_id ?? $contract->id),
            'notes' => 'Automatically generated from contract ' . ($contract->contract_id ?? $contract->id),
        ]);

        foreach ($data['items'] as $item) {
            $purchaseRequest->items()->create([
                'description' => $item['name'],
                'quantity' => $item['quantity'],
                'unit' => $item['unit'],
                'estimated_unit_price' => $item['unitCost'],
                'total_amount' => $item['totalCost'],
            ]);
        }

        $contractNumber = $contract->contract_id ?? $contract->id;

        return response()->json([
            'success' => true,
            'pr_number' => $prNumber,
            'contract_number' => $contractNumber,
            'pr_id' => $purchaseRequest->id,
        ]);
    }
} 