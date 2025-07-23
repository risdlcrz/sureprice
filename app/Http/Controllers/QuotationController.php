<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\PurchaseRequest;
use App\Models\Material;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\QuotationAttachment;
use App\Models\QuotationResponseAttachment;
use App\Models\Activity;

class QuotationController extends Controller
{
    private function logPageView($description, $modelType = null, $modelId = null)
    {
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'viewed',
            'description' => $description,
            'model_type' => $modelType,
            'model_id' => $modelId
        ]);
    }

    public function index(Request $request)
    {
        $this->logPageView('Viewed Quotation List', Quotation::class);
        $query = Quotation::with(['purchaseRequest', 'suppliers', 'materials']);

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('rfq_number', 'like', "%$search%")
                  ->orWhere('notes', 'like', "%$search%")
                  ->orWhereHas('purchaseRequest', function($q2) use ($search) {
                      $q2->where('department', 'like', "%$search%")
                         ->orWhere('purpose', 'like', "%$search%")
                         ->orWhere('pr_number', 'like', "%$search%") ;
                  })
                  ->orWhereHas('suppliers', function($q3) use ($search) {
                      $q3->where('company_name', 'like', "%$search%")
                         ->orWhere('contact_person', 'like', "%$search%")
                         ->orWhere('email', 'like', "%$search%") ;
                  })
                  ->orWhereHas('materials', function($q4) use ($search) {
                      $q4->where('name', 'like', "%$search%")
                         ->orWhere('description', 'like', "%$search%") ;
                  });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        } elseif ($request->input('tab') === 'supplier') {
            $query->where('status', 'in_progress');
        }

        // Purchase Request filter
        if ($request->filled('purchase_request')) {
            $query->where('purchase_request_id', $request->input('purchase_request'));
        }

        // Per page
        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        $supplierQuotations = $query->latest()->paginate($perPage)->appends($request->all());

        $purchaseRequests = PurchaseRequest::where('status', 'approved')
            ->orderBy('id')
            ->get();

        $quotationRequests = \App\Models\QuotationRequest::with('user')->latest()->get();

        return view('admin.quotations.index', compact('quotationRequests', 'supplierQuotations', 'purchaseRequests'));
    }

    public function create()
    {
        $this->logPageView('Viewed Create Quotation Page', Quotation::class);
        $purchaseRequests = PurchaseRequest::with(['items.material'])
            ->where('status', 'approved')
            ->orderBy('id')
            ->get();
        $suppliers = Supplier::orderBy('company_name')->get();
        return view('admin.quotations.form', compact('purchaseRequests', 'suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'purchase_request_id' => 'nullable|exists:purchase_requests,id',
            'is_standalone' => 'sometimes|boolean',
            'due_date' => 'required|date|after:today',
            'suppliers' => 'required|array',
            'suppliers.*' => 'exists:suppliers,id',
            'supplier_notes' => 'array',
            'supplier_notes.*' => 'nullable|string',
            'notes' => 'nullable|string',
            'payment_terms' => 'nullable|string',
            'delivery_terms' => 'nullable|string',
            'validity_period' => 'nullable|string',
            'attachments.*' => 'nullable|file|max:10240',
            'materials' => 'array', // For standalone quotations
            'materials.*.id' => 'required_with:materials|exists:materials,id',
            'materials.*.quantity' => 'required_with:materials|numeric|min:0.01',
        ]);

        // Validate material requirements based on quotation type
        if ($request->boolean('is_standalone')) {
            $request->validate([
                'materials' => 'required|array|min:1', // Materials are required for standalone
            ]);
            $purchaseRequestId = null; // No PR for standalone
        } else {
            $request->validate([
                'purchase_request_id' => 'required|exists:purchase_requests,id', // PR is required if not standalone
            ]);
            $purchaseRequestId = $validated['purchase_request_id'];
        }

        // Generate sequential RFQ number
        $lastQuotation = Quotation::orderByDesc('id')->first();
        if ($lastQuotation && preg_match('/RFQ-(\\d+)/i', $lastQuotation->rfq_number, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }
        $rfqNumber = 'RFQ-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        $quotation = Quotation::create([
            'purchase_request_id' => $purchaseRequestId,
            'due_date' => $validated['due_date'],
            'notes' => $validated['notes'],
            'payment_terms' => $validated['payment_terms'],
            'delivery_terms' => $validated['delivery_terms'],
            'validity_period' => $validated['validity_period'],
            'status' => 'draft',
            'rfq_number' => $rfqNumber
        ]);

        // Attach suppliers with notes
        foreach ($validated['suppliers'] as $supplierId) {
            $quotation->suppliers()->attach($supplierId, [
                'notes' => $validated['supplier_notes'][$supplierId] ?? null
            ]);
        }

        // Attach materials for standalone quotations
        if ($request->boolean('is_standalone') && !empty($validated['materials'])) {
            $materialSyncData = [];
            foreach ($validated['materials'] as $materialData) {
                $materialSyncData[$materialData['id']] = ['quantity' => $materialData['quantity']];
            }
            $quotation->materials()->sync($materialSyncData);
        }

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('quotations/' . $quotation->id);
                $quotation->attachments()->create([
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName()
                ]);
            }
        }

        // Set total_amount (if applicable, though for RFQ it's usually later)
        // For now, this can be null or calculated based on PR if not standalone
        if (!$request->boolean('is_standalone') && $purchaseRequestId) {
            $purchaseRequest = PurchaseRequest::with('items')->find($purchaseRequestId);
            $totalAmount = $purchaseRequest->items->sum('total_amount');
            $quotation->total_amount = $totalAmount;
        }
        $quotation->save();

        // Log activity
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'created',
            'description' => 'Created Quotation #' . $quotation->rfq_number,
            'model_type' => Quotation::class,
            'model_id' => $quotation->id
        ]);

        return redirect()->route('quotations.index')
            ->with('success', 'RFQ created successfully.');
    }

    public function show(Quotation $quotation)
    {
        $this->logPageView('Viewed Quotation #' . $quotation->rfq_number, Quotation::class, $quotation->id);
        $quotation->load(['purchaseRequest', 'suppliers', 'responses.items', 'responses.attachments']);
        return view('admin.quotations.show', compact('quotation'));
    }

    public function edit(Quotation $quotation)
    {
        $this->logPageView('Viewed Edit Quotation #' . $quotation->rfq_number, Quotation::class, $quotation->id);
        if (!in_array($quotation->status, ['draft', 'sent'])) {
            return redirect()->route('quotations.show', $quotation)
                ->with('error', 'This RFQ cannot be edited.');
        }

        $purchaseRequests = PurchaseRequest::with(['items.material'])
            ->where('status', 'approved')
            ->orderBy('id')
            ->get();
        $suppliers = Supplier::orderBy('company_name')->get();
        $quotation->load(['purchaseRequest', 'suppliers', 'responses.items', 'responses.attachments']);
        return view('admin.quotations.form', compact('quotation', 'purchaseRequests', 'suppliers'));
    }

    public function update(Request $request, Quotation $quotation)
    {
        if (!in_array($quotation->status, ['draft', 'sent'])) {
            return redirect()->route('quotations.show', $quotation)
                ->with('error', 'This RFQ cannot be updated.');
        }

        $validated = $request->validate([
            'purchase_request_id' => 'nullable|exists:purchase_requests,id',
            'is_standalone' => 'sometimes|boolean',
            'due_date' => 'required|date|after:today',
            'suppliers' => 'required|array',
            'suppliers.*' => 'exists:suppliers,id',
            'supplier_notes' => 'array',
            'supplier_notes.*' => 'nullable|string',
            'notes' => 'nullable|string',
            'payment_terms' => 'nullable|string',
            'delivery_terms' => 'nullable|string',
            'validity_period' => 'nullable|string',
            'attachments.*' => 'nullable|file|max:10240',
            'materials' => 'array',
            'materials.*.id' => 'required_with:materials|exists:materials,id',
            'materials.*.quantity' => 'required_with:materials|numeric|min:0.01',
        ]);

        // Conditional validation for materials based on quotation type
        $isStandalone = $request->boolean('is_standalone');
        $purchaseRequestId = null;

        if ($isStandalone) {
            $request->validate([
                'materials' => 'required|array|min:1',
            ]);
        } else {
            $request->validate([
                'purchase_request_id' => 'required|exists:purchase_requests,id',
            ]);
            $purchaseRequestId = $validated['purchase_request_id'];
        }

        // Update quotation details
        $quotation->update([
            'purchase_request_id' => $purchaseRequestId,
            'due_date' => $validated['due_date'],
            'notes' => $validated['notes'],
            'payment_terms' => $validated['payment_terms'],
            'delivery_terms' => $validated['delivery_terms'],
            'validity_period' => $validated['validity_period']
        ]);

        // Sync suppliers with notes
        $supplierSync = [];
        foreach ($validated['suppliers'] as $supplierId) {
            $supplierSync[$supplierId] = [
                'notes' => $validated['supplier_notes'][$supplierId] ?? null
            ];
        }
        $quotation->suppliers()->sync($supplierSync);

        // Sync materials based on quotation type
        if ($isStandalone) {
            $materialSyncData = [];
            foreach ($validated['materials'] as $materialData) {
                $materialSyncData[$materialData['id']] = ['quantity' => $materialData['quantity']];
            }
            $quotation->materials()->sync($materialSyncData);
        } else {
            // If not standalone, clear any direct materials attached previously
            $quotation->materials()->detach();
        }

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('quotations/' . $quotation->id);
                $quotation->attachments()->create([
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName()
                ]);
            }
        }

        // Set total_amount (if applicable, though for RFQ it's usually later)
        if (!$isStandalone && $purchaseRequestId) {
            $purchaseRequest = PurchaseRequest::with('items')->find($purchaseRequestId);
            $totalAmount = $purchaseRequest->items->sum('total_amount');
            $quotation->total_amount = $totalAmount;
        } else {
            $quotation->total_amount = null;
        }
        $quotation->save();

        // Log activity
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'updated',
            'description' => 'Updated Quotation #' . $quotation->rfq_number,
            'model_type' => Quotation::class,
            'model_id' => $quotation->id
        ]);

        return redirect()->route('quotations.show', $quotation)
            ->with('success', 'RFQ updated successfully.');
    }

    public function destroy(Quotation $quotation)
    {
        // Delete attachments from storage
        foreach ($quotation->attachments as $attachment) {
            Storage::delete($attachment->path);
        }

        $rfqNumber = $quotation->rfq_number;
        $quotation->delete();

        // Log activity
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'deleted',
            'description' => 'Deleted Quotation #' . $rfqNumber,
            'model_type' => Quotation::class,
            'model_id' => $quotation->id
        ]);

        return redirect()->route('quotations.index')
            ->with('success', 'RFQ deleted successfully.');
    }

    public function send(Quotation $quotation)
    {
        if ($quotation->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Only draft RFQs can be sent.'
            ]);
        }

        // Here you would typically send emails to suppliers
        // For now, we'll just update the status
        $quotation->update(['status' => 'sent']);

        return response()->json(['success' => true]);
    }

    public function approve(Request $request, Quotation $quotation)
    {
        // Ensure only admin can approve/reject
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'status' => 'required|in:approved,rejected',
            'admin_notes' => 'nullable|string',
        ]);

        $quotation->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return response()->json([
            'message' => 'Quotation has been ' . $request->status . '.',
            'quotation' => $quotation->fresh()
        ]);
    }

    public function reject(Quotation $quotation)
    {
        // Ensure only admin can approve/reject
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        $quotation->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(), // Admin who rejected
            'approved_at' => now(),
        ]);

        return response()->json([
            'message' => 'Quotation has been rejected.',
            'quotation' => $quotation->fresh()
        ]);
    }

    public function removeAttachment(Request $request)
    {
        $validated = $request->validate([
            'quotation_id' => 'required|exists:quotations,id',
            'attachment_id' => 'required|exists:attachments,id'
        ]);

        $quotation = Quotation::findOrFail($validated['quotation_id']);
        $attachment = $quotation->attachments()->findOrFail($validated['attachment_id']);

        Storage::delete($attachment->path);
        $attachment->delete();

        return response()->json(['success' => true]);
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        $purchaseRequestId = $request->get('purchase_request_id');
        
        $quotations = Quotation::with(['purchaseRequest', 'suppliers', 'materials'])
            ->when($query, function($q) use ($query) {
                return $q->where('rfq_number', 'like', "%{$query}%")
                    ->orWhere('notes', 'like', "%{$query}%");
            })
            ->when($purchaseRequestId, function($q) use ($purchaseRequestId) {
                return $q->where('purchase_request_id', $purchaseRequestId);
            })
            ->latest()
            ->get();
            
        return response()->json($quotations);
    }

    public function downloadAttachment($id)
    {
        $attachment = QuotationAttachment::findOrFail($id);
        return Storage::download($attachment->path, $attachment->original_name);
    }

    public function downloadResponseAttachment($id)
    {
        $attachment = QuotationResponseAttachment::findOrFail($id);
        return Storage::download($attachment->path, $attachment->file_name);
    }

    public function showJson($id)
    {
        $qr = \App\Models\QuotationRequest::with(['user', 'rooms.scopes'])->findOrFail($id);

        // Build client address (customize as needed)
        $client = $qr->user;
        $clientAddress = trim("{$client->street} {$client->barangay} {$client->city} {$client->state} {$client->postal}");

        // Gather items and compute totals
        $items = [];
        $totalMaterialCost = 0;
        foreach ($qr->rooms as $room) {
            foreach ($room->scopes as $scope) {
                if (is_array($scope->selected_materials)) {
                    foreach ($scope->selected_materials as $mat) {
                        $material = \App\Models\Material::find($mat['material_id']);
                        $unitPrice = $material ? $material->price : 0;
                        $qty = $mat['quantity'];
                        $amount = $unitPrice * $qty;
                        $items[] = [
                            'material' => [
                                'name' => $material ? $material->name : 'Unknown',
                            ],
                            'quantity' => $qty,
                            'unit_price' => $unitPrice,
                            'amount' => $amount,
                        ];
                        $totalMaterialCost += $amount;
                    }
                }
            }
        }
        $laborCost = $totalMaterialCost * 0.15;
        $grandTotal = $totalMaterialCost + $laborCost;

        return response()->json([
            'client' => [
                'name' => $client->name,
                'address' => $clientAddress,
                'street' => $client->street,
                'city' => $client->city,
                'state' => $client->state,
                'postal' => $client->postal,
                'email' => $client->email,
                'phone' => $client->phone,
            ],
            'property' => [
                'address' => $qr->property_address ?? '', // Adjust as needed
                'street' => $qr->property_street ?? '',
                'city' => $qr->property_city ?? '',
                'state' => $qr->property_state ?? '',
                'postal' => $qr->property_postal ?? '',
            ],
            'items' => $items,
            'total_material_cost' => $totalMaterialCost,
            'labor_cost' => $laborCost,
            'grand_total' => $grandTotal,
            // Add more fields as needed
        ]);
    }
} 